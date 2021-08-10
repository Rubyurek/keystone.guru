<?php

namespace Database\Seeders;

use App\Models\Affix;
use App\Models\AffixGroup;
use App\Models\AffixGroupCoupling;
use App\Models\File;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AffixSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->_rollback();


        $this->command->info('Adding known affixes');

        $affixes = [
            new Affix(['name' => 'Bolstering', 'icon_file_id' => -1, 'description' => 'When any non-boss enemy dies, its death cry empowers nearby allies, increasing their maximum health and damage by 20%.']),
            new Affix(['name' => 'Bursting', 'icon_file_id' => -1, 'description' => 'When slain, non-boss enemies explode, causing all players to suffer 10% of their max health in damage over 4 sec. This effect stacks.']),
            new Affix(['name' => 'Explosive', 'icon_file_id' => -1, 'description' => 'While in combat, enemies periodically summon Explosive Orbs that will detonate if not destroyed.']),
            new Affix(['name' => 'Fortified', 'icon_file_id' => -1, 'description' => 'Non-boss enemies have 20% more health and inflict up to 30% increased damage.']),
            new Affix(['name' => 'Grievous', 'icon_file_id' => -1, 'description' => 'When injured below 90% health, players will suffer increasing damage over time until healed above 90% health.']),
            new Affix(['name' => 'Infested', 'icon_file_id' => -1, 'description' => 'Some non-boss enemies have been infested with a Spawn of G\'huun.']),
            new Affix(['name' => 'Necrotic', 'icon_file_id' => -1, 'description' => 'All enemies\' melee attacks apply a stacking blight that inflicts damage over time and reduces healing received.']),
            new Affix(['name' => 'Quaking', 'icon_file_id' => -1, 'description' => 'Periodically, all players emit a shockwave, inflicting damage and interrupting nearby allies.']),
            new Affix(['name' => 'Raging', 'icon_file_id' => -1, 'description' => 'Non-boss enemies enrage at 30% health remaining, dealing 100% increased damage until defeated.']),
            new Affix(['name' => 'Relentless', 'icon_file_id' => -1, 'description' => 'Non-boss enemies are granted temporary immunity to Loss of Control effects.']),
            new Affix(['name' => 'Sanguine', 'icon_file_id' => -1, 'description' => 'When slain, non-boss enemies leave behind a lingering pool of ichor that heals their allies and damages players.']),
            new Affix(['name' => 'Skittish', 'icon_file_id' => -1, 'description' => 'Enemies pay far less attention to threat generated by tanks.']),
            new Affix(['name' => 'Teeming', 'icon_file_id' => -1, 'description' => 'Additional non-boss enemies are present throughout the dungeon.']),
            new Affix(['name' => 'Tyrannical', 'icon_file_id' => -1, 'description' => 'Boss enemies have 40% more health and inflict up to 15% increased damage.']),
            new Affix(['name' => 'Volcanic', 'icon_file_id' => -1, 'description' => 'While in combat, enemies periodically cause gouts of flame to erupt beneath the feet of distant players.']),

            new Affix(['name' => 'Reaping', 'icon_file_id' => -1, 'description' => 'Non-boss enemies are empowered by Bwonsamdi and periodically seek vengeance from beyond the grave.']),
            new Affix(['name' => 'Beguiling', 'icon_file_id' => -1, 'description' => 'Azshara\'s Emissaries are present throughout the dungeon.']),
            new Affix(['name' => 'Awakened', 'icon_file_id' => -1, 'description' => 'Obelisks throughout the dungeon allow players to enter Ny\'alotha and confront powerful servants of N\'Zoth. If a servant is not dealt with, they must be faced during the final boss encounter.']),

            new Affix(['name' => 'Inspiring', 'icon_file_id' => -1, 'description' => 'Some non-boss enemies have an inspiring presence that strengthens their allies.']),
            new Affix(['name' => 'Spiteful', 'icon_file_id' => -1, 'description' => 'Fiends rise from the corpses of non-boss enemies and pursue random players.']),
            new Affix(['name' => 'Storming', 'icon_file_id' => -1, 'description' => 'While in combat, enemies periodically summon damaging whirlwinds.']),

            new Affix(['name' => 'Prideful', 'icon_file_id' => -1, 'description' => 'Players overflow with pride as they defeat non-boss enemies, eventually forming a Manifestation of Pride. Defeating this Manifestation greatly empowers players.']),
            new Affix(['name' => 'Tormented', 'icon_file_id' => -1, 'description' => 'Servants of the Jailer can be found throughout the dungeon and grant powerful boons when defeated. If a servant is not dealt with, they empower the final boss.']),
            new Affix(['name' => 'Unknown', 'icon_file_id' => -1, 'description' => 'The affixes for this week are not known yet.']),
        ];

        foreach ($affixes as $affix) {
            /** @var $affix \Illuminate\Database\Eloquent\Model */
            $affix->save();

            $iconName = strtolower(str_replace(' ', '', $affix->name));
            $icon = new File();
            $icon->model_id = $affix->id;
            $icon->model_class = get_class($affix);
            $icon->disk = 'public';
            $icon->path = sprintf('images/affixes/%s.jpg', $iconName);
            $icon->save();

            $affix->icon_file_id = $icon->id;
            $affix->save();
        }

        $groups = [
            ['season_id' => 1, 'seasonal_index' => 0, 'affixes' => ['Fortified', 'Sanguine', 'Necrotic', 'Infested']],
            ['season_id' => 1, 'seasonal_index' => 1, 'affixes' => ['Tyrannical', 'Bursting', 'Skittish', 'Infested']],
            ['season_id' => 1, 'seasonal_index' => 2, 'affixes' => ['Fortified', 'Teeming', 'Quaking', 'Infested']],
            ['season_id' => 1, 'seasonal_index' => 0, 'affixes' => ['Tyrannical', 'Raging', 'Necrotic', 'Infested']],
            ['season_id' => 1, 'seasonal_index' => 1, 'affixes' => ['Fortified', 'Bolstering', 'Skittish', 'Infested']],
            ['season_id' => 1, 'seasonal_index' => 2, 'affixes' => ['Tyrannical', 'Teeming', 'Volcanic', 'Infested']],
            ['season_id' => 1, 'seasonal_index' => 0, 'affixes' => ['Fortified', 'Sanguine', 'Grievous', 'Infested']],
            ['season_id' => 1, 'seasonal_index' => 1, 'affixes' => ['Tyrannical', 'Bolstering', 'Explosive', 'Infested']],
            ['season_id' => 1, 'seasonal_index' => 2, 'affixes' => ['Fortified', 'Bursting', 'Quaking', 'Infested']],
            ['season_id' => 1, 'seasonal_index' => 0, 'affixes' => ['Tyrannical', 'Raging', 'Volcanic', 'Infested']],
            ['season_id' => 1, 'seasonal_index' => 1, 'affixes' => ['Fortified', 'Teeming', 'Explosive', 'Infested']],
            ['season_id' => 1, 'seasonal_index' => 2, 'affixes' => ['Tyrannical', 'Bolstering', 'Grievous', 'Infested']],

            ['season_id' => 2, 'affixes' => ['Fortified', 'Sanguine', 'Necrotic', 'Reaping']],
            ['season_id' => 2, 'affixes' => ['Tyrannical', 'Bursting', 'Skittish', 'Reaping']],
            ['season_id' => 2, 'affixes' => ['Fortified', 'Teeming', 'Quaking', 'Reaping']],
            ['season_id' => 2, 'affixes' => ['Tyrannical', 'Raging', 'Necrotic', 'Reaping']],
            ['season_id' => 2, 'affixes' => ['Fortified', 'Bolstering', 'Skittish', 'Reaping']],
            ['season_id' => 2, 'affixes' => ['Tyrannical', 'Teeming', 'Volcanic', 'Reaping']],
            ['season_id' => 2, 'affixes' => ['Fortified', 'Teeming', 'Quaking', 'Reaping']],
            ['season_id' => 2, 'affixes' => ['Tyrannical', 'Raging', 'Necrotic', 'Reaping']],
            ['season_id' => 2, 'affixes' => ['Fortified', 'Bolstering', 'Skittish', 'Reaping']],
            ['season_id' => 2, 'affixes' => ['Tyrannical', 'Teeming', 'Volcanic', 'Reaping']],
            ['season_id' => 2, 'affixes' => ['Fortified', 'Teeming', 'Explosive', 'Reaping']],
            ['season_id' => 2, 'affixes' => ['Tyrannical', 'Bolstering', 'Grievous', 'Reaping']],

            ['season_id' => 3, 'seasonal_index' => 1, 'affixes' => ['Fortified', 'Bolstering', 'Skittish', 'Beguiling']],
            ['season_id' => 3, 'seasonal_index' => 2, 'affixes' => ['Tyrannical', 'Bursting', 'Necrotic', 'Beguiling']],
            ['season_id' => 3, 'seasonal_index' => 0, 'affixes' => ['Fortified', 'Sanguine', 'Quaking', 'Beguiling']],
            ['season_id' => 3, 'seasonal_index' => 1, 'affixes' => ['Tyrannical', 'Bolstering', 'Explosive', 'Beguiling']],
            ['season_id' => 3, 'seasonal_index' => 2, 'affixes' => ['Fortified', 'Bursting', 'Volcanic', 'Beguiling']],
            ['season_id' => 3, 'seasonal_index' => 0, 'affixes' => ['Tyrannical', 'Raging', 'Necrotic', 'Beguiling']],
            ['season_id' => 3, 'seasonal_index' => 1, 'affixes' => ['Fortified', 'Teeming', 'Quaking', 'Beguiling']],
            ['season_id' => 3, 'seasonal_index' => 2, 'affixes' => ['Tyrannical', 'Bursting', 'Skittish', 'Beguiling']],
            ['season_id' => 3, 'seasonal_index' => 0, 'affixes' => ['Fortified', 'Bolstering', 'Grievous', 'Beguiling']],
            ['season_id' => 3, 'seasonal_index' => 1, 'affixes' => ['Tyrannical', 'Raging', 'Explosive', 'Beguiling']],
            ['season_id' => 3, 'seasonal_index' => 2, 'affixes' => ['Fortified', 'Sanguine', 'Grievous', 'Beguiling']],
            ['season_id' => 3, 'seasonal_index' => 0, 'affixes' => ['Tyrannical', 'Teeming', 'Volcanic', 'Beguiling']],

            ['season_id' => 4, 'seasonal_index' => 0, 'affixes' => ['Fortified', 'Bolstering', 'Skittish', 'Awakened']],
            ['season_id' => 4, 'seasonal_index' => 1, 'affixes' => ['Tyrannical', 'Bursting', 'Necrotic', 'Awakened']],
            ['season_id' => 4, 'seasonal_index' => 1, 'affixes' => ['Fortified', 'Sanguine', 'Quaking', 'Awakened']],
            ['season_id' => 4, 'seasonal_index' => 0, 'affixes' => ['Tyrannical', 'Bolstering', 'Explosive', 'Awakened']],
            ['season_id' => 4, 'seasonal_index' => 0, 'affixes' => ['Fortified', 'Bursting', 'Volcanic', 'Awakened']],
            ['season_id' => 4, 'seasonal_index' => 1, 'affixes' => ['Tyrannical', 'Raging', 'Necrotic', 'Awakened']],
            ['season_id' => 4, 'seasonal_index' => 1, 'affixes' => ['Fortified', 'Teeming', 'Quaking', 'Awakened']],
            ['season_id' => 4, 'seasonal_index' => 0, 'affixes' => ['Tyrannical', 'Bursting', 'Skittish', 'Awakened']],
            ['season_id' => 4, 'seasonal_index' => 0, 'affixes' => ['Fortified', 'Bolstering', 'Grievous', 'Awakened']],
            ['season_id' => 4, 'seasonal_index' => 1, 'affixes' => ['Tyrannical', 'Raging', 'Explosive', 'Awakened']],
            ['season_id' => 4, 'seasonal_index' => 1, 'affixes' => ['Fortified', 'Sanguine', 'Grievous', 'Awakened']],
            ['season_id' => 4, 'seasonal_index' => 0, 'affixes' => ['Tyrannical', 'Teeming', 'Volcanic', 'Awakened']],

            ['season_id' => 5, 'affixes' => ['Fortified', 'Spiteful', 'Grievous', 'Prideful']],
            ['season_id' => 5, 'affixes' => ['Tyrannical', 'Inspiring', 'Necrotic', 'Prideful']],
            ['season_id' => 5, 'affixes' => ['Fortified', 'Sanguine', 'Quaking', 'Prideful']],
            ['season_id' => 5, 'affixes' => ['Tyrannical', 'Raging', 'Explosive', 'Prideful']],
            ['season_id' => 5, 'affixes' => ['Fortified', 'Spiteful', 'Volcanic', 'Prideful']],
            ['season_id' => 5, 'affixes' => ['Tyrannical', 'Bolstering', 'Necrotic', 'Prideful']],
            ['season_id' => 5, 'affixes' => ['Fortified', 'Inspiring', 'Storming', 'Prideful']],
            ['season_id' => 5, 'affixes' => ['Tyrannical', 'Bursting', 'Explosive', 'Prideful']],
            ['season_id' => 5, 'affixes' => ['Fortified', 'Sanguine', 'Grievous', 'Prideful']],
            ['season_id' => 5, 'affixes' => ['Tyrannical', 'Raging', 'Quaking', 'Prideful']],
            ['season_id' => 5, 'affixes' => ['Fortified', 'Bursting', 'Volcanic', 'Prideful']],
            ['season_id' => 5, 'affixes' => ['Tyrannical', 'Bolstering', 'Storming', 'Prideful']],

            ['season_id' => 6, 'seasonal_index' => 0, 'affixes' => ['Tyrannical', 'Unknown', 'Unknown', 'Tormented']],
            ['season_id' => 6, 'seasonal_index' => 1, 'affixes' => ['Fortified', 'Unknown', 'Unknown', 'Tormented']],
            ['season_id' => 6, 'seasonal_index' => 1, 'affixes' => ['Tyrannical', 'Unknown', 'Unknown', 'Tormented']],
            ['season_id' => 6, 'seasonal_index' => 0, 'affixes' => ['Fortified', 'Bursting', 'Storming', 'Tormented']],
            ['season_id' => 6, 'seasonal_index' => 0, 'affixes' => ['Tyrannical', 'Raging', 'Volcanic', 'Tormented']],
            ['season_id' => 6, 'seasonal_index' => 1, 'affixes' => ['Fortified', 'Inspiring', 'Grievous', 'Tormented']],
            ['season_id' => 6, 'seasonal_index' => 1, 'affixes' => ['Tyrannical', 'Spiteful', 'Necrotic', 'Tormented']],
            ['season_id' => 6, 'seasonal_index' => 0, 'affixes' => ['Fortified', 'Bolstering', 'Quaking', 'Tormented']],
            ['season_id' => 6, 'seasonal_index' => 0, 'affixes' => ['Tyrannical', 'Sanguine', 'Storming', 'Tormented']],
            ['season_id' => 6, 'seasonal_index' => 1, 'affixes' => ['Fortified', 'Raging', 'Explosive', 'Tormented']],
            ['season_id' => 6, 'seasonal_index' => 1, 'affixes' => ['Tyrannical', 'Unknown', 'Unknown', 'Tormented']],
            ['season_id' => 6, 'seasonal_index' => 0, 'affixes' => ['Fortified', 'Unknown', 'Unknown', 'Tormented']],
        ];

        foreach ($groups as $groupArr) {
            $group = AffixGroup::create([
                'season_id'      => $groupArr['season_id'],
                'seasonal_index' => $groupArr['seasonal_index'] ?? null
            ]);

            foreach ($groupArr['affixes'] as $affixName) {
                $affix = $this->_findAffix($affixes, $affixName);

                AffixGroupCoupling::create([
                    'affix_id'       => $affix->id,
                    'affix_group_id' => $group->id
                ]);
            }
        }
    }

    /**
     * Finds an affix by name in a list of affixes.
     *
     * @param $affixes
     * @param $affixName
     * @return bool|Affix
     */
    private function _findAffix($affixes, $affixName)
    {
        $result = false;

        foreach ($affixes as $affix) {
            if ($affix->name === $affixName) {
                $result = $affix;
                break;
            }
        }

        if (!$result) {
            $this->command->error(sprintf('Unable to find affix %s', $affixName));
        }

        return $result;
    }

    private function _rollback()
    {
        DB::table('affixes')->truncate();
        DB::table('affix_groups')->truncate();
        DB::table('affix_group_couplings')->truncate();
        DB::table('files')->where('model_class', 'App\Models\Affix')->delete();
    }
}
