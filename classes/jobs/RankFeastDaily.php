<?php

namespace Cleanse\Feast\Classes\Jobs;

use Cleanse\Feast\Classes\SoloRankingsUpdate;
use Cleanse\Feast\Classes\PartyRankingsUpdate;

class RankFeastDaily
{
    public function fire($job, $data)
    {
        if ($data['type'] == 'solo') {
            $crawl = new SoloRankingsUpdate;
            $crawl->dailyPlayerSort($data);
        }

        if ($data['type'] == 'party') {
            $crawl = new PartyRankingsUpdate;
            $crawl->dailyPlayerSort($data);
        }

        $job->delete();
    }
}
