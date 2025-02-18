<?php


namespace App\Service\Expansion;

use App\Models\AffixGroup\AffixGroup;
use App\Models\Expansion;
use App\Models\GameServerRegion;
use App\Models\Season;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Collection;

class ExpansionService implements ExpansionServiceInterface
{
    /**
     * @inheritDoc
     */
    public function getExpansionAt(Carbon $carbon): ?Expansion
    {
        return Expansion::where('released_at', '<', $carbon->toDateTimeString())
            ->orderBy('released_at', 'desc')
            ->first();
    }

    /**
     * @inheritDoc
     */
    public function getCurrentExpansion(): Expansion
    {
        return $this->getExpansionAt(Carbon::now());
    }

    /**
     * @return Expansion|null
     */
    public function getNextExpansion(): ?Expansion
    {
        return $this->getExpansionAt(Carbon::now()->addWeeks(4));
    }

    /**
     * @inheritDoc
     */
    public function getData(Expansion $expansion): ExpansionData
    {
        return new ExpansionData($this, $expansion);
    }

    /**
     * @inheritDoc
     */
    public function getCurrentSeason(Expansion $expansion): ?Season
    {
        return $expansion->currentseason;
    }

    /**
     * @inheritDoc
     */
    public function getActiveDungeons(Expansion $expansion): Collection
    {
        return $expansion->dungeons;
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    public function getCurrentAffixGroup(Expansion $expansion, GameServerRegion $gameServerRegion): ?AffixGroup
    {
        return optional($this->getCurrentSeason($expansion))->getCurrentAffixGroupInRegion($gameServerRegion);
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    public function getNextAffixGroup(Expansion $expansion, GameServerRegion $gameServerRegion): ?AffixGroup
    {
        return optional($this->getCurrentSeason($expansion))->getNextAffixGroupInRegion($gameServerRegion);
    }

    /**
     * @inheritDoc
     */
    public function getCurrentSeasonAffixGroups(Expansion $expansion): Collection
    {
        $currentSeason = $this->getCurrentSeason($expansion);
        return $currentSeason !== null ? $currentSeason->affixgroups()
            ->with(['affixes:affixes.id,affixes.key,affixes.name,affixes.description'])
            ->get() : collect();
    }
}
