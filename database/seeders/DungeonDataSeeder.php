<?php

namespace Database\Seeders;

use App\Logic\Utils\Stopwatch;
use App\Models\DungeonRoute;
use App\Models\Expansion;
use Database\Seeders\RelationImport\Mapping\DungeonFloorSwitchMarkerRelationMapping;
use Database\Seeders\RelationImport\Mapping\DungeonRelationMapping;
use Database\Seeders\RelationImport\Mapping\DungeonRouteRelationMapping;
use Database\Seeders\RelationImport\Mapping\EnemyPackRelationMapping;
use Database\Seeders\RelationImport\Mapping\EnemyPatrolRelationMapping;
use Database\Seeders\RelationImport\Mapping\EnemyRelationMapping;
use Database\Seeders\RelationImport\Mapping\MapIconRelationMapping;
use Database\Seeders\RelationImport\Mapping\NpcRelationMapping;
use Database\Seeders\RelationImport\Mapping\RelationMapping;
use Database\Seeders\RelationImport\Mapping\SpellRelationMapping;
use Database\Seeders\RelationImport\Parsers\RelationParser;
use Exception;
use FilesystemIterator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DungeonDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     * @throws Exception
     */
    public function run()
    {
        // Just a base class
        $this->_rollback();

        $this->command->info('Starting import of dungeon data for all dungeons');

        $mappings = [
            // Loose files
            new DungeonRelationMapping(),
            new NpcRelationMapping(),
            new DungeonRouteRelationMapping(),
            new SpellRelationMapping(),

            // Files inside floor folder
            new EnemyRelationMapping(),

            new EnemyPackRelationMapping(),
            new EnemyPatrolRelationMapping(),
            new DungeonFloorSwitchMarkerRelationMapping(),
            new MapIconRelationMapping(),
        ];

        $rootDir = database_path('/seeders/dungeondata/');
        $rootDirIterator = new FilesystemIterator($rootDir);

        // For each expansion
        foreach ($rootDirIterator as $rootDirChild) {
            $rootDirChildBaseName = basename($rootDirChild);

            // Only folders which have the correct shortname
            if (Expansion::where('shortname', $rootDirChildBaseName)->first() !== null) {
                $this->command->info('Expansion ' . $rootDirChildBaseName);
                $expansionDirIterator = new FilesystemIterator($rootDirChild);

                // For each dungeon inside an expansion dir
                foreach ($expansionDirIterator as $dungeonKeyDir) {
                    $this->command->info('- Importing dungeon ' . basename($dungeonKeyDir));

                    $floorDirIterator = new FilesystemIterator($dungeonKeyDir);
                    // For each floor inside a dungeon dir
                    foreach ($floorDirIterator as $floorDirFile) {
                        // Parse loose files
                        if (!is_dir($floorDirFile)) {
                            // npcs, dungeon_routes
                            $this->_parseRawFile($rootDir, $floorDirFile, $mappings, 2);
                        } // Parse floor dir
                        else {
                            $this->command->info('-- Importing floor ' . basename($floorDirFile));

                            $importFileIterator = new FilesystemIterator($floorDirFile);
                            // For each file inside a floor
                            foreach ($importFileIterator as $importFile) {
                                $this->_parseRawFile($rootDir, $importFile, $mappings, 3);
                            }
                        }
                    }
                }
            } // It's a 'global' file, parse it
            else if (strpos($rootDirChild, '.json') === strlen($rootDirChild) - 5) { // 5 for length of .json
                $this->_parseRawFile($rootDir, $rootDirChild, $mappings, 1);
            }
        }

        Stopwatch::dumpAll();
    }

    /**
     * @param $rootDir string
     * @param $filePath string
     * @param $mappings RelationMapping[]
     * @param $depth integer
     * @throws Exception
     */
    private function _parseRawFile(string $rootDir, string $filePath, array $mappings, int $depth = 1): void
    {
        $prefix = str_repeat('-', $depth) . ' ';

        $fileName = basename($filePath);

        // Import file
        $this->command->info($prefix . 'Importing ' . $fileName);
        $found = false;
        foreach ($mappings as $mapping) {
            if ($mapping->getFileName() === $fileName) {
                $count = $this->_loadModelsFromFile($filePath, $mapping);
                $this->command->info(sprintf(
                    $prefix . 'Imported %s (%s into %s)',
                    str_replace($rootDir, '', $fileName),
                    $count,
                    $fileName
                ));

                $found = true;
                break;
            }
        }

        // Let the user know if something wrong happened
        if (!$found) {
            $this->command->error($prefix . 'Unable to find table->model mapping for file ' . $filePath);
        }
    }

    /**
     * @param $filePath string
     * @param $mapping RelationMapping
     * @return int The amount of models loaded from the file
     * @throws Exception
     */
    private function _loadModelsFromFile(string $filePath, RelationMapping $mapping): int
    {
        // Load contents
        $modelJson = file_get_contents($filePath);
        // Convert to models
        $modelsData = json_decode($modelJson, true);

        // Pre-fetch all valid columns to make the below loop a bit faster
        $modelColumns = Schema::getColumnListing($mapping->getClass()::newModelInstance()->getTable());

        // In case there's no post-save relation parsers we can mass-save instead to save time
        $modelsToSave = collect();

        // Do some php fuckery to make this a bit cleaner
        foreach ($modelsData as $modelData) {

            $unsetRelations = [];

            for ($i = 0; $i < count($modelData); $i++) {
                $keys = array_keys($modelData);
                $key = $keys[$i];
                $value = $modelData[$key];

                // $this->command->info(json_encode($key) . ' ' . json_encode($value));

                foreach ($mapping->getPreSaveAttributeParsers() as $attributeParser) {
                    // Relations are always arrays, so exclude those that are not, then verify if the parser can handle this, then if it can, parse it
                    if (is_array($value) &&
                        $attributeParser->canParseModel($mapping->getClass()) &&
                        $attributeParser->canParseRelation($key, $value)) {

                        $modelData = $attributeParser->parseRelation($mapping->getClass(), $modelData, $key, $value);
                    }
                }

                // The column may not be set due to objects appearing in this array that need to be de-normalized
                if (!in_array($key, $modelColumns)) {
                    // Keep track of all relations we removed so we can parse them again after saving the model
                    $unsetRelations[$key] = $value;
                    unset($modelData[$key]);
                    $i--;
                }
            }

            // $this->command->info("Creating model " . json_encode($modelData));
            // Create and save a new instance to the database

            /** @var Model $createdModel */
            // Check if we need to update this model instead of saving it
            if (isset($modelData['id']) && $mapping->isPersistent()) {
                // Load first
                $createdModel = $mapping->getClass()::findOrNew($modelData['id']);
                // Apply, then save
                $createdModel->setRawAttributes($modelData);
                $createdModel->save();

            } // If we should do some post processing, create & save it now so that we can do just that
            else if ($mapping->getPostSaveAttributeParsers()->isNotEmpty()) {
                $createdModel = $mapping->getClass()::create($modelData);
            } // We don't need to do post processing, add it to the list to be saved
            else {
                $modelsToSave->push($modelData);
                continue;
            }

            // If we have models to mass-save later, we should not do post-processing since it's incompatible
            if ($modelsToSave->isEmpty()) {
                $modelData['id'] = $createdModel->id;

                // Merge the unset relations with the model again so we can parse the model again
                $modelData = $modelData + $unsetRelations;

                foreach ($mapping->getPostSaveAttributeParsers() as $attributeParser) {
                    foreach ($modelData as $key => $value) {
                        /** @var $attributeParser RelationParser */
                        // Relations are always arrays, so exclude those that are not, then verify if the parser can handle this, then if it can, parse it
                        if (is_array($value) &&
                            $attributeParser->canParseModel($mapping->getClass()) &&
                            $attributeParser->canParseRelation($key, $value)) {

                            // Ignore return value, use preModelSaveAttributeParser if you want the parser to have effect on the
                            // model that's about to be saved. It's already saved at this point
                            $attributeParser->parseRelation($mapping->getClass(), $modelData, $key, $value);
                        }
                    }
                }
            }
        }

        // Bulk save the models that did not need any post-attribute parsing
        if ($modelsToSave->isNotEmpty()) {
            $mapping->getClass()::insert($modelsToSave->toArray());
        }

        // $this->command->info('OK _loadModelsFromFile ' . $filePath . ' ' . $modelClassName);
        return count($modelsData);
    }

    protected function _rollback()
    {
        $this->command->warn('Truncating all relevant data...');

        // Can DEFINITELY NOT truncate DungeonRoute table here. That'd wipe the entire instance, not good.
        /** @var DungeonRoute[]|Collection $demoRoutes */
        $demoRoutes = DungeonRoute::where('demo', true)->get();

        // Delete each found route that was a demo (controlled by me only)
        // This will remove all killzones, brushlines, paths etc related to the route.
        foreach ($demoRoutes as $demoRoute) {
            try {
                $demoRoute->delete();
            } catch (Exception $ex) {
                $this->command->error(sprintf('%s: Exception deleting demo dungeonroute', $ex->getMessage()));
            }
        }


        DB::table('spells')->truncate();
        DB::table('npcs')->truncate();
        DB::table('npc_bolstering_whitelists')->truncate();
        DB::table('npc_spells')->truncate();
        DB::table('enemies')->truncate();
        DB::table('enemy_packs')->truncate();
        DB::table('enemy_patrols')->truncate();
        DB::table('dungeon_floor_switch_markers')->truncate();
        // Delete all map icons that are always there
        DB::table('map_icons')->where('dungeon_route_id', -1)->delete();
        // Delete polylines related to enemy patrols
        DB::table('polylines')->where('model_class', 'App\Models\EnemyPatrol')->delete();

        // Truncating these before the above will cause some issues
        // Do not truncate dungeons - we want to keep the active state of dungeons unique for each environment, if we truncate it it'd be reset
        // DB::table('dungeons')->truncate();
        DB::table('floors')->truncate();
        DB::table('floor_couplings')->truncate();
    }
}
