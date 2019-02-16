<?php

namespace Cleanse\Feast;

use Config;
use DateTime;
use Event;
use Log;
use Queue;
use System\Classes\PluginBase;
use Cleanse\PvPaissa\Classes\HelperDataCenters;
use Cleanse\Feast\Classes\FeastHelper;

class Plugin extends PluginBase
{
    public function pluginDetails()
    {
        return [
            'name'          => 'PvPaissa Feast Plugin',
            'description'   => 'Adds FFXIV Feast Rankings to PvPaissa.',
            'author'        => 'Paul Lovato',
            'icon'          => 'icon-shield'
        ];
    }

    public function registerComponents()
    {
        return [
            'Cleanse\Feast\Components\Solo'     => 'cleanseFeastSolo',
            'Cleanse\Feast\Components\Party'    => 'cleanseFeastParty',
            'Cleanse\Feast\Components\Profile'  => 'cleanseFeastProfile',
            'Cleanse\Feast\Components\Install'  => 'cleanseFeastInstall',

            /**
             * todo: Create stats page from s1-current
             */
            'Cleanse\Feast\Components\Stats'    => 'cleanseFeastStats',
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
        $schedule->call(function () {
            $feast = new FeastHelper;
            $day = $feast->yearDay();
            $tiers = $feast->tiers;
            $types = $feast->types;
            $datacenters = $feast->datacenters;
            $season = Config::get('cleanse.feast::season', 1);

            Log::info('Starting feast update.');

            foreach ($types as $type) {
                foreach ($datacenters as $datacenter) {
                    foreach ($tiers as $tier) {
                        $data = [
                            'datacenter' => $datacenter,
                            'day' => $day,
                            'tier' => $tier,
                            'type' => $type,
                            'season' => $season
                        ];

                        if ($type === 'party' and $tier <= 4) {
                            Log::info('Skipping: '.$datacenter.' '.$type.' '.$tier);
                        } else {
                            Log::info('else '.$datacenter.' '.$type.' '.$tier.' '.$season);
                            Queue::push('\Cleanse\Feast\Classes\Jobs\ScrapeFeast', $data);
                        }
                    }
                }

                Queue::push('\Cleanse\Feast\Classes\Jobs\RankFeastDaily', [
                    'day' => $day,
                    'type' => $type,
                    'season' => $season
                ]);
            }
        })->cron('45 14 * * *');
    }
}
