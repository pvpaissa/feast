<?php

namespace Cleanse\Feast\Classes\LightParty;

use Cleanse\Feast\Classes\LightParty\RankingsUpdate;

class QueueRankings
{
    public function fire($job, $data)
    {
        $crawl = new RankingsUpdate;
        $crawl->dailyPartySort($data);

        $job->delete();
    }
}
