<?php


namespace App\Service\DungeonRoute;

use App\Models\AffixGroup;
use App\Models\Dungeon;
use App\Models\DungeonRoute;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class DevDiscoverService extends BaseDiscoverService
{
    /**
     * Gets a builder that provides a template for popular routes.
     *
     * @return Builder
     */
    private function popularBuilder(): Builder
    {
        return DungeonRoute::query()->limit(10)
            ->when($this->closure !== null, $this->closure)
            ->select('dungeon_routes.*')
            ->with(['author', 'affixes', 'ratings'])
            ->without(['faction', 'specializations', 'classes', 'races'])
            ->join('dungeons', 'dungeon_routes.dungeon_id', '=', 'dungeons.id')
            ->where('dungeons.expansion_id', $this->expansion->id)
//            ->where('dungeon_routes.published_state_id', PublishedState::where('name', PublishedState::WORLD)->first()->id)
            ->whereNull('dungeon_routes.expires_at')
            ->where('demo', false);
    }

    /**
     * Gets a builder that provides a template for popular routes.
     *
     * @return Builder
     */
    private function newBuilder(): Builder
    {
        return DungeonRoute::query()->limit(10)
            ->when($this->closure !== null, $this->closure)
            ->select('dungeon_routes.*')
            ->with(['author', 'affixes', 'ratings'])
            ->without(['faction', 'specializations', 'classes', 'races'])
            ->join('dungeons', 'dungeon_routes.dungeon_id', '=', 'dungeons.id')
            ->where('dungeons.expansion_id', $this->expansion->id)
//            ->where('dungeon_routes.published_state_id', PublishedState::where('name', PublishedState::WORLD)->first()->id)
            ->whereNull('dungeon_routes.expires_at')
            ->where('demo', false)
            ->orderBy('published_at', 'desc');
    }

    /**
     * @inheritDoc
     */
    function popular(): Collection
    {
        return $this->popularBuilder()->get();
    }

    /**
     * @inheritDoc
     */
    function popularGroupedByDungeon(): Collection
    {
        return $this->popularBuilder()->get();
    }

    /**
     * @inheritDoc
     */
    function popularByAffixGroup(AffixGroup $affixGroup): Collection
    {
        return $this->popularBuilder()->get();
    }

    /**
     * @inheritDoc
     */
    function popularGroupedByDungeonByAffixGroup(AffixGroup $affixGroup): Collection
    {
        return $this->popularBuilder()->get();
    }

    /**
     * @inheritDoc
     */
    function popularByDungeon(Dungeon $dungeon): Collection
    {
        return $this->popularBuilder()->get();
    }

    /**
     * @inheritDoc
     */
    function popularByDungeonAndAffixGroup(Dungeon $dungeon, AffixGroup $affixGroup): Collection
    {
        return $this->popularBuilder()->get();
    }

    /**
     * @inheritDoc
     */
    function new(): Collection
    {
        return $this->newBuilder()->get();
    }

    /**
     * @inheritDoc
     */
    function newByAffixGroup(AffixGroup $affixGroup): Collection
    {
        return $this->newBuilder()->get();
    }

    /**
     * @inheritDoc
     */
    function newByDungeon(Dungeon $dungeon): Collection
    {
        return $this->newBuilder()->get();
    }

    /**
     * @inheritDoc
     */
    function newByDungeonAndAffixGroup(Dungeon $dungeon, AffixGroup $affixGroup): Collection
    {
        return $this->newBuilder()->get();
    }

    /**
     * @inheritDoc
     */
    function popularUsers(): Collection
    {
        // TODO: Implement popularUsers() method.
        return collect();
    }

    /**
     * @inheritDoc
     */
    function popularUsersByAffixGroup(AffixGroup $affixGroup): Collection
    {
        // TODO: Implement popularUsersByAffixGroup() method.
        return collect();
    }

    /**
     * @inheritDoc
     */
    function popularUsersByDungeon(Dungeon $dungeon): Collection
    {
        // TODO: Implement popularUsersByDungeon() method.
        return collect();
    }

    /**
     * @inheritDoc
     */
    function popularUsersByDungeonAndAffixGroup(Dungeon $dungeon, AffixGroup $affixGroup): Collection
    {
        // TODO: Implement popularUsersByDungeonAndAffixGroup() method.
        return collect();
    }
}
