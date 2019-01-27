<?php

namespace App\Http\Controllers;

use App\Logic\MDT\IO\ImportString;
use App\Models\AffixGroup;
use App\Models\MDTImport;
use Illuminate\Http\Request;

class MDTImportController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {

    }

    /**
     * Returns some details about the passed string.
     * @param Request $request
     * @return array
     * @throws \Exception
     */
    public function details(Request $request)
    {
        $string = $request->get('import_string');

        $importString = new ImportString();

        try {
            $dungeonRoute = $importString->setEncodedString($string)->getDungeonRoute(false);

            $affixes = [];
            foreach ($dungeonRoute->affixes as $affixGroup) {
                /** @var $affixGroup AffixGroup */
                $affixes[] = $affixGroup->getTextAttribute();
            }

            $result = [
                'dungeon' => $dungeonRoute->dungeon !== null ? $dungeonRoute->dungeon->name : __('Unknown dungeon'),
                'affixes' => $affixes,
                'pulls' => $dungeonRoute->killzones->count(),
                'lines' => $dungeonRoute->polylines->count(),
                'notes' => $dungeonRoute->mapcomments->count(),
                'enemy_forces' => $dungeonRoute->getEnemyForcesAttribute(),
                'enemy_forces_max' => $dungeonRoute->hasTeemingAffix() ? $dungeonRoute->dungeon->enemy_forces_required_teeming : $dungeonRoute->dungeon->enemy_forces_required
            ];

            return $result;
        } catch (\Exception $ex) {
            abort(400, sprintf(__('Invalid MDT string: %s'), $ex->getMessage()));
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Exception
     */
    public function import(Request $request)
    {
        $string = $request->get('import_string');

        $importString = new ImportString();

        // @TODO improve exception handling
        $dungeonRoute = $importString->setEncodedString($string)->getDungeonRoute(true);

        // Keep track of the import
        $mdtImport = new MDTImport();
        $mdtImport->dungeon_route_id = $dungeonRoute->id;
        $mdtImport->import_string = $string;
        $mdtImport->save();


        return redirect()->route('dungeonroute.edit', ['dungeonroute' => $dungeonRoute]);
    }
}
