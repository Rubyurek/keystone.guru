<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $floor_id
 * @property int $dungeon_route_id
 * @property int $map_icon_type_id
 * @property float $lat
 * @property float $lng
 * @property string $comment
 *
 * @property \App\Models\DungeonRoute $dungeonroute
 * @property \App\User $user
 *
 * @mixin \Eloquent
 */
class MapIcon extends Model
{
    const MAP_COMMENT = 'mapcomment';

    protected $visible = ['id', 'floor_id', 'map_icon_type_id', 'lat', 'lng', 'comment'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    function floor()
    {
        return $this->belongsTo('App\Models\Floor');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    function dungeonroute()
    {
        return $this->belongsTo('App\Models\DungeonRoute');
    }
}
