<?php

namespace App\Models\Mapping;

use App\Models\Dungeon;
use App\Models\DungeonFloorSwitchMarker;
use App\Models\Enemy;
use App\Models\EnemyPack;
use App\Models\EnemyPatrol;
use App\Models\MapIcon;
use App\Models\MountableArea;
use Carbon\Carbon;
use Eloquent;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

/**
 * @property int $id
 * @property int $dungeon_id
 * @property int $version
 * @property bool $merged Not saved in the database
 *
 * @property Carbon $updated_at
 * @property Carbon $created_at
 *
 * @property Dungeon $dungeon
 * @property Collection|DungeonFloorSwitchMarker[] $dungeonFloorSwitchMarkers
 * @property Collection|Enemy[] $enemies
 * @property Collection|EnemyPack[] $enemyPacks
 * @property Collection|EnemyPatrol[] $enemyPatrols
 * @property Collection|MapIcon[] $mapIcons
 * @property Collection|MountableArea[] $mountableAreas
 *
 * @mixin Eloquent
 */
class MappingVersion extends Model
{
    protected $visible = [
        'id',
        'dungeon_id',
        'version',
        'merged',
    ];

    protected $fillable = [
        'dungeon_id',
        'version',
        'updated_at',
        'created_at',
    ];

    protected $appends = [
        'merged',
    ];

    public $timestamps = true;

    /**
     * @return bool
     */
    public function getMergedAttribute(): bool
    {
        $mostRecentlyMergedMappingCommitLog = MappingCommitLog::where('merged', 1)->orderBy('id', 'desc')->first();

        return $mostRecentlyMergedMappingCommitLog !== null && $mostRecentlyMergedMappingCommitLog->created_at->gte($this->created_at);
    }


    /**
     * @return BelongsTo
     */
    public function dungeon(): BelongsTo
    {
        return $this->belongsTo(Dungeon::class);
    }

    /**
     * @return HasMany
     */
    public function dungeonFloorSwitchMarkers(): HasMany
    {
        return $this->hasMany(DungeonFloorSwitchMarker::class);
    }

    /**
     * @return HasMany
     */
    public function enemies(): HasMany
    {
        return $this->hasMany(Enemy::class);
    }

    /**
     * @return HasMany
     */
    public function enemyPacks(): HasMany
    {
        return $this->hasMany(EnemyPack::class);
    }

    /**
     * @return HasMany
     */
    public function enemyPatrols(): HasMany
    {
        return $this->hasMany(EnemyPatrol::class);
    }

    /**
     * @return HasMany
     */
    public function mapIcons(): HasMany
    {
        return $this->hasMany(MapIcon::class);
    }

    /**
     * @return HasMany
     */
    public function mountableAreas(): HasMany
    {
        return $this->hasMany(MountableArea::class);
    }


    public static function boot()
    {
        parent::boot();

        // If we create a new mapping version, we must create a complete copy of the previous mapping and re-save that to the database.
        static::created(function (MappingVersion $newMappingVersion) {
            /** @var Collection|MappingVersion[] $existingMappingVersions */
            $existingMappingVersions = $newMappingVersion->dungeon->mappingversions()->get();
            // We must get the previous mapping version - that contains the mapping we want to clone
            $previousMappingVersion = $existingMappingVersions[1];

            /** @var Collection|Model[] $previousMapping */
            $previousMapping = collect()
                ->merge($previousMappingVersion->dungeonFloorSwitchMarkers)
                ->merge($previousMappingVersion->enemies)
                ->merge($previousMappingVersion->enemyPacks)
                ->merge($previousMappingVersion->enemyPatrols)
                ->merge($previousMappingVersion->mapIcons)
                ->merge($previousMappingVersion->mountableAreas);

            // Take the giant list of models and re-save them one by one for the new version of the mapping
            foreach ($previousMapping as $model) {
                $model->exists             = false;
                $model->id                 = null;
                $model->mapping_version_id = $newMappingVersion->id;
                $model->save();
            }
        });
    }
}