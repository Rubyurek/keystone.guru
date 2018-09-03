<?php

namespace App\Http\Controllers;

use App\Models\Dungeon;
use App\Models\DungeonFloorSwitchMarker;
use App\Models\DungeonStartMarker;
use App\Models\Enemy;
use App\Models\EnemyPack;
use App\Models\EnemyPatrol;
use App\Models\Floor;
use App\Models\Npc;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class ExportDungeonDataController extends Controller
{
    /**
     * @param Request $request
     * @return mixed
     * @throws \Exception
     */
    public function submit(Request $request)
    {
        $result = array();

        foreach (Dungeon::all() as $dungeon) {
            /** @var $dungeon Dungeon */
            $rootDirPath = storage_path() . '/dungeondata/' . $dungeon->expansion->shortname . '/' . $dungeon->key;

            // Demo routes, load it in a specific way to make it easier to import it back in again
            $demoRoutes = $dungeon->dungeonroutes->where('demo', '=', true)->values();
            foreach ($demoRoutes as $demoRoute) {
                /** @var $demoRoute Model */
                unset($demoRoute->relations);
                // Do not reload them
                $demoRoute->setAppends([]);
                // Ids cannot be guaranteed with users uploading dungeonroutes as well. As such, a new internal ID must be created
                // for each and every re-import
                $demoRoute->setHidden(['id']);
                $demoRoute->load(['playerraces', 'playerclasses', 'affixgroups', 'routes']);
            }

            $this->_saveData($demoRoutes, $rootDirPath, 'dungeonroutes.json');

            $npcs = Npc::all()->where('dungeon_id', '=', $dungeon->id)->values();

            // Save NPC data in the root of the dungeon folder
            $this->_saveData($npcs, $rootDirPath, 'npcs.json');

            /** @var Dungeon $dungeon */
            foreach ($dungeon->floors as $floor) {
                /** @var Floor $floor */
                $enemies = Enemy::all()->where('floor_id', '=', $floor->id)->values();
                $enemyPacks = EnemyPack::all()->where('floor_id', '=', $floor->id)->values();
                $enemyPatrols = EnemyPatrol::all()->where('floor_id', '=', $floor->id)->values();
                $dungeonStartMarkers = DungeonStartMarker::all()->where('floor_id', '=', $floor->id)->values();
                $dungeonFloorSwitchMarkers = DungeonFloorSwitchMarker::all()->where('floor_id', '=', $floor->id)->values();

                $result['enemies'] = $enemies;
                $result['enemy_packs'] = $enemyPacks;
                $result['enemy_patrols'] = $enemyPatrols;
                $result['dungeon_start_markers'] = $dungeonStartMarkers;
                $result['dungeon_floor_switch_markers'] = $dungeonFloorSwitchMarkers;

                foreach ($result as $category => $categoryData) {
                    // Save enemies, packs, patrols, markers on a per-floor basis
                    $this->_saveData($categoryData, $rootDirPath . '/' . $floor->index, $category . '.json');
                }
            }
        }

        return view('admin.datadump.viewexporteddungeondata', ['data' => $result]);
    }

    /**
     * @param $dataArr Collection
     * @param $dir string
     * @param $filename string
     */
    private function _saveData($dataArr, $dir, $filename)
    {
        if (!file_exists($dir)) {
            mkdir($dir, 755, true);
        }

        $filePath = $dir . '/' . $filename;
        $file = fopen($filePath, 'w') or die('Cannot create file');
        fwrite($file, json_encode($dataArr, JSON_PRETTY_PRINT));
        fclose($file);
    }

    /**
     * Handles the viewing of a collection of items in a table.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\
     */
    public function view()
    {
        return view('admin.datadump.exportdungeondata', ['dungeons' => Dungeon::all()]);
    }
}
