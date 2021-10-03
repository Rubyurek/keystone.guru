<?php


namespace App\Logic\MapContext;

use App\Models\AffixGroup;
use App\Models\DungeonRoute;
use App\Models\DungeonRouteEnemyRaidMarker;
use App\Models\RaidMarker;

/**
 * Trait DungeonRouteTrait
 * @package App\Logic\MapContext
 *
 * @mixin MapContext
 */
trait DungeonRouteTrait
{
    /**
     * @param DungeonRoute $dungeonRoute
     * @return array
     */
    private function getDungeonRouteProperties(DungeonRoute $dungeonRoute): array
    {
        $raidMarkers = RaidMarker::all();

        return [
            'publicKey'               => $dungeonRoute->public_key,
            'teamId'                  => $dungeonRoute->team_id,
            'pullGradient'            => $dungeonRoute->pull_gradient,
            'pullGradientApplyAlways' => $dungeonRoute->pull_gradient_apply_always,
            'faction'                 => strtolower($dungeonRoute->faction->name),
            'enemyForces'             => $dungeonRoute->enemy_forces,

            // Relations
            'killZones'               => $dungeonRoute->killzones,
            'mapIcons'                => $dungeonRoute->mapicons,
            'paths'                   => $dungeonRoute->paths,
            'brushlines'              => $dungeonRoute->brushlines,
            'pridefulenemies'         => $dungeonRoute->pridefulenemies,
            'enemyRaidMarkers'        => $dungeonRoute->enemyraidmarkers->map(function (DungeonRouteEnemyRaidMarker $drEnemyRaidMarker) use ($raidMarkers) {
                return [
                    'enemy_id'         => $drEnemyRaidMarker->enemy_id,
                    'raid_marker_name' => $raidMarkers->where('id', $drEnemyRaidMarker->raid_marker_id)->first()->name,
                ];
            }),
            // A list of affixes that this route has (not to be confused with AffixGroups)
            'uniqueAffixes'           => $dungeonRoute->affixes->map(function (AffixGroup $affixGroup) {
                return $affixGroup->affixes;
            })->collapse()->unique()->pluck(['name'])->map(function (string $name) {
                return __($name);
            }),
        ];
    }
}
