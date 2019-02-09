<?php

namespace Cleanse\Feast\Classes\Jobs;

use Cleanse\Feast\Classes\SoloRankingsUpdate;
use Cleanse\Feast\Classes\PartyRankingsUpdate;

class RankFeastSeason
{
    public function fire($job, $data)
    {
        if ($data['type'] == 'solo') {
            $crawl = new SoloRankingsUpdate;
            $crawl->seasonPlayerSort($data);
        }

        if ($data['type'] == 'party') {
            $crawl = new PartyRankingsUpdate;
            $crawl->seasonPlayerSort($data);
        }

        $job->delete();
    }
}
