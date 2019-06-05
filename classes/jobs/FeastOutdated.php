<?php

namespace Cleanse\Feast\Classes\Jobs;

use Cleanse\Feast\Classes\RankingsOutdated;

class FeastOutdated
{
    public function fire($job, $data)
    {
        if ($data['type'] == 'solo') {
            $fix = new RankingsOutdated;
            $fix->updateSolo($data);
        }

        if ($data['type'] == 'party') {
            $fix = new RankingsOutdated;
            $fix->updateParty($data);
        }

        if ($data['type'] == 'lp') {
            $fix = new RankingsOutdated;
            $fix->updateLightParty($data);
        }

        $job->delete();
    }
}
