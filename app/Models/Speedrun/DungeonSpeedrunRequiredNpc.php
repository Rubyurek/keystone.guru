<?php

namespace App\Models\Speedrun;

use App\Models\Dungeon;
use App\Models\Npc;
use Eloquent;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $dungeon_id
 * @property int $npc_id
 * @property int $count
 *
 * @property Dungeon $dungeon
 * @property Npc $npc
 *
 * @mixin Eloquent
 */
class DungeonSpeedrunRequiredNpc extends Model
{
    protected $fillable = [
        'dungeon_id',
        'npc_id',
        'count',
    ];
    protected $with = ['dungeon', 'npc'];
    public $timestamps = false;

    /**
     * @return BelongsTo
     */
    public function dungeon(): BelongsTo
    {
        return $this->belongsTo(Dungeon::class);
    }

    /**
     * @return BelongsTo
     */
    public function npc(): BelongsTo
    {
        return $this->belongsTo(Npc::class);
    }
}