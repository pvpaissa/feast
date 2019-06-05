<?php

namespace Cleanse\Feast\Classes\LightParty;

use Queue;
use Cleanse\Feast\Classes\FeastHelper;

class Schedule
{
    public function crawlData($season)
    {
        $feast = new FeastHelper;
        $day         = $feast->yearDay();
        $datacenters = $feast->datacenters;

        foreach ($datacenters as $datacenter) {
            $data = [
                'datacenter' => $datacenter,
                'day'        => $day,
                'season'     => $season
            ];

            Queue::push('\Cleanse\Feast\Classes\LightParty\QueueUpdate', $data);
        }
    }

    public function calculateRankings($season)
    {
        $feast = new FeastHelper;
        $day = $feast->yearDay();

        Queue::push('\Cleanse\Feast\Classes\LightParty\QueueRankings', ['day' => $day, 'season' => $season]);
    }
}
