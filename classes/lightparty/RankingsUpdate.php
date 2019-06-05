<?php

namespace Cleanse\Feast\Classes\LightParty;

use Queue;
use Cleanse\PvPaissa\Classes\HelperRankSort;
use Cleanse\Feast\Classes\LightParty\UpdateParty;
use Cleanse\Feast\Classes\LightParty\PartyCrawler;
use Cleanse\Feast\Models\Party;
use Cleanse\Feast\Models\PartyDaily;

class RankingsUpdate
{
    public function updateDay($data)
    {
        $list = new PartyCrawler($data['season'], $data['datacenter'], $data['day']);

        $parties = $list->crawl();

        if (empty($parties)) {
            return;
        }

        foreach ($parties as $party) {
            $lp = new UpdateParty($data['season'], $party);
            $lp->update();
        }
    }

    public function dailyPartySort($data)
    {
        $parties = PartyDaily::where('day', $data['day'])->get(['id', 'rating'])->toArray();

        if (!empty($parties)) {
            $sort = new HelperRankSort;
            $updatedParties = $sort->sortRanks($parties, 'rating');

            PartyDaily::where('day', $data['day'])
                ->orderBy('rating', 'desc')
                ->chunk(200, function ($parties) use ($updatedParties) {
                    foreach ($parties as $party) {
                        if (isset($updatedParties[$party->id]) && !empty($updatedParties[$party->id])) {
                            $party->rank = $updatedParties[$party->id];

                            $party->save();
                        }
                    }
                });
        }

        $typeSeason = ['season' => $data['season'], 'type' => 'lp'];
        Queue::push('\Cleanse\Feast\Classes\Jobs\FeastOutdated', $typeSeason);
        Queue::push('\Cleanse\Feast\Classes\Jobs\RankFeastSeason', $typeSeason);
    }

    public function seasonPartySort($data)
    {
        $parties = Party::where('season', $data['season'])->get(['id', 'rating'])->toArray();

        if (!empty($parties)) {
            $sort = new HelperRankSort;
            $updatedParties = $sort->sortRanks($parties, 'rating');

            Party::where('season', $data['season'])
                ->orderBy('rating', 'desc')
                ->chunk(200, function ($parties) use ($updatedParties) {
                    foreach ($parties as $party) {
                        if (isset($updatedParties[$party->id]) && !empty($updatedParties[$party->id])) {
                            $party->rank = $updatedParties[$party->id];

                            $party->save();
                        }
                    }
                });
        }
    }
}
