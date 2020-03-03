<?php


use App\Models\MapIcon;

class DungeonRouteMapIconsRelationParser implements RelationParser
{
    /**
     * @param $modelClassName string
     * @return mixed
     */
    public function canParseModel($modelClassName)
    {
        return $modelClassName === 'App\Models\DungeonRoute';
    }

    /**
     * @param $name string
     * @param $value array
     * @return mixed
     */
    public function canParseRelation($name, $value)
    {
        return $name === 'mapicons' && is_array($value);
    }

    /**
     * @param $modelClassName string
     * @param $modelData array
     * @param $name string
     * @param $value array
     * @return array
     */
    public function parseRelation($modelClassName, $modelData, $name, $value)
    {
        foreach ($value as $mapIconData) {
            // We now know the dungeon route ID, set it back to the map comment
            $mapIconData['dungeon_route_id'] = $modelData['id'];

            MapIcon::insert($mapIconData);
        }

        // Didn't really change anything so just return the value.
        return $modelData;
    }

}