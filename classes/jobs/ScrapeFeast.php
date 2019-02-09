<?php

namespace Cleanse\Feast\Classes\Jobs;

use Cleanse\Feast\Classes\SoloRankingsUpdate;
use Cleanse\Feast\Classes\PartyRankingsUpdate;

class ScrapeFeast
{
    public function fire($job, $data)
    {
        if ($data['type'] == 'solo') {
            $crawl = new SoloRankingsUpdate;
            $crawl->updateDay($data);
        }

        if ($data['type'] == 'party') {
            $crawl = new PartyRankingsUpdate;
            $crawl->updateDay($data);
        }

        $job->delete();
    }
}
