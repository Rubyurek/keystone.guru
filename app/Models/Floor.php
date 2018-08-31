<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $dungeon_id
 * @property int $index
 * @property string $name
 * @property Dungeon $dungeon
 * @property \Illuminate\Support\Collection $enemypacks
 * @property \Illuminate\Support\Collection $connectedFloors
 * @property \Illuminate\Support\Collection $directConnectedFloors
 * @property \Illuminate\Support\Collection $reverseConnectedFloors
 */
class Floor extends Model
{
    public $hidden = ['dungeon_id', 'created_at', 'updated_at'];

    public function dungeon()
    {
        return $this->belongsTo('App\Models\Dungeon');
    }

    function enemies()
    {
        return $this->hasMany('App\Models\Enemy');
    }

    function enemypacks()
    {
        return $this->hasMany('App\Models\EnemyPack');
    }

    function enemypatrols()
    {
        return $this->hasMany('App\Models\EnemyPatrol');
    }

    /**
     * @return \Illuminate\Support\Collection A list of all connected floors, regardless of direction
     */
    public function connectedFloors()
    {
        return $this->directConnectedFloors->merge($this->reverseConnectedFloors);
    }

    public function directConnectedFloors()
    {
        return $this->belongsToMany('App\Models\Floor', 'floor_couplings', 'floor1_id', 'floor2_id');
    }

    public function reverseConnectedFloors()
    {
        return $this->belongsToMany('App\Models\Floor', 'floor_couplings', 'floor2_id', 'floor1_id');
    }
}
