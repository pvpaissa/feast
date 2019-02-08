<?php

namespace Cleanse\Feast;

use Config;
use DateTime;
use Event;
use Log;
use Queue;
use System\Classes\PluginBase;
use Cleanse\Pvpaissa\Models\Player;
use Cleanse\Pvpaissa\Classes\HelperDataCenters;
use Cleanse\Pvpaissa\Classes\Feast\FeastHelper;

class Plugin extends PluginBase
{
    public function pluginDetails()
    {
        return [
            'name' => 'PvPaissa Feast Plugin',
            'description' => 'Adds FFXIV Feast Rankings to PvPaissa.',
            'author' => 'Paul Lovato',
            'icon' => 'icon-shield'
        ];
    }

    public function registerComponents()
    {
        return [
            'Cleanse\Pvpaissa\Components\FeastSoloFull'         => 'feastSoloFull',
            'Cleanse\Pvpaissa\Components\FeastSoloMini'         => 'feastSoloMini',
            'Cleanse\Pvpaissa\Components\FeastPartyFull'        => 'feastPartyFull',
            'Cleanse\Pvpaissa\Components\FeastPartyMini'        => 'feastPartyMini',
            'Cleanse\Pvpaissa\Components\FeastWeekly'           => 'feastWeekly',
            'Cleanse\Pvpaissa\Components\CharacterProfileFeast' => 'characterProfileFeast',
            'Cleanse\Pvpaissa\Components\FeastInstall'          => 'feastInstall', //?? Rewrite later.
        ];
    }

    public function registerMarkupTags()
    {
        return [
            'filters' => [
                'yearweek' => [$this, 'makeDateFromYearWeek']
            ]
        ];
    }

    public function makeDateFromYearWeek($yearWeek)
    {
        $year = substr($yearWeek, 0, -2);
        $week = substr($yearWeek, 4);

        $date = new DateTime();

        $date->setISODate($year, $week);
        return $date->format('Y-m-d');
    }

    public function registerSchedule($schedule)
    {
//        $schedule->call(function () {
//            $feast = new FeastHelper;
//            $day = $feast->yearDay();
//            $tiers = $feast->tiers;
//            $types = $feast->types;
//            $datacenters = $feast->datacenters;
//            $season = Config::get('cleanse.pvpaissa::season', 1);
//
//            Log::info('Starting feast update.');
//
//            foreach ($types as $type) {
//                foreach ($datacenters as $datacenter) {
//                    foreach ($tiers as $tier) {
//                        $data = [
//                            'datacenter' => $datacenter,
//                            'day' => $day,
//                            'tier' => $tier,
//                            'type' => $type,
//                            'season' => $season
//                        ];
//
//                        if ($type === 'party' and $tier <= 4) {
//                            Log::info('Skipping: '.$datacenter.' '.$type.' '.$tier);
//                        } else {
//                            Log::info('else '.$datacenter.' '.$type.' '.$tier.' '.$season);
//                            Queue::push('\Cleanse\Pvpaissa\Classes\Jobs\ScrapeFeast', $data);
//                        }
//                    }
//                }
//
//                Queue::push('\Cleanse\Pvpaissa\Classes\Jobs\RankFeastDaily', [
//                    'day' => $day,
//                    'type' => $type,
//                    'season' => $season
//                ]);
//            }
//        })->cron('45 14 * * *');
    }
}
