<?php

namespace Cleanse\Feast\Components;

use Config;
use Queue;
use Cms\Classes\ComponentBase;
use Cleanse\Feast\Classes\FeastHelper;

class Install extends ComponentBase
{
    public $test;

    public function componentDetails()
    {
        return [
            'name'          => 'PvPaissa Feast Installer.',
            'description'   => 'Adds completed seasons to the database.'
        ];
    }

    /**
     * Look at frontlines updater. Refactor!
     */
    public function onRun()
    {
        $this->install();
    }

    public function install()
    {
        $feast = new FeastHelper;
        $day = $feast->yearDay();
        $tiers = $feast->tiers;
        $types = $feast->types;
        $datacenters = $feast->datacenters;
        $season = Config::get('cleanse.feast::season', 2) - 1;

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
                        return;
                    } else {
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
    }
}
