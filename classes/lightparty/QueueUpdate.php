<?php

namespace Cleanse\Feast\Classes\LightParty;

use Cleanse\Feast\Classes\LightParty\RankingsUpdate;

class QueueUpdate
{
    public function fire($job, $data)
    {
        $crawl = new RankingsUpdate;
        $crawl->updateDay($data);

        $job->delete();
    }
}
