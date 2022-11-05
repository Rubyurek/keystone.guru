<?php

namespace App\SeederHelpers\RelationImport\Parsers\Relation;

use App\Models\DungeonRoute;
use App\Models\KillZone;
use App\Models\KillZoneEnemy;

class DungeonRouteKillZoneRelationParser implements RelationParserInterface
{
    /**
     * @param string $modelClassName
     * @return bool
     */
    public function canParseRootModel(string $modelClassName): bool
    {
        return false;
    }

    /**
     * @param string $modelClassName
     * @return bool
     */
    public function canParseModel(string $modelClassName): bool
    {
        return $modelClassName === DungeonRoute::class;
    }

    /**
     * @param string $name
     * @param array $value
     * @return bool
     */
    public function canParseRelation(string $name, array $value): bool
    {
        return $name === 'killzones';
    }

    /**
     * @param string $modelClassName
     * @param array $modelData
     * @param string $name
     * @param array $value
     * @return array
     */
    public function parseRelation(string $modelClassName, array $modelData, string $name, array $value): array
    {
        foreach ($value as $killZoneData) {
            // We now know the dungeon route ID, set it back to the Route
            $killZoneData['dungeon_route_id'] = $modelData['id'];

            // Unset the relation data, otherwise the save function will complain that the column doesn't exist,
            // but keep a reference to it as we still need it later on
            $enemies = $killZoneData['killzoneenemies'];
            unset($killZoneData['killzoneenemies']);

            if (count($enemies) > 0) {
                // Gotta save the KillZone in order to get an ID
                $killZone = new KillZone($killZoneData);
                $killZone->save();

                foreach ($enemies as $key => $enemy) {
                    // Make sure the enemy's relation with the kill zone is restored.
                    // Do not use $enemy since that would create a new copy and we'd lose our changes
                    $enemies[$key]['kill_zone_id'] = $killZone->id;
                }

                // Insert vertices
                KillZoneEnemy::insert($enemies);
            }
        }

        // Didn't really change anything so just return the value.
        return $modelData;
    }

}