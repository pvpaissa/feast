<?php

namespace Cleanse\Feast;

use DateTime;
use System\Classes\PluginBase;
use Cleanse\Feast\Classes\LightParty\Schedule;

class Plugin extends PluginBase
{
    private $season;

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
        //This is the first true LP season not tied to FRC.
        $this->season = 1;

        //Look into adding a backend for new seasons and finalizing seasons 'SoonTM'
        //If we do, we'll add logic below to schedule the diff modes.
        $schedule->call(function () {
            $getLP = new Schedule;
            $getLP->crawlData($this->season);
        })->cron('3 4 * * *');

        $schedule->call(function ()
        {
            $getLP = new Schedule;
            $getLP->calculateRankings($this->season);
        })->cron('33 4 * * *');
    }
}
