<?php

namespace Cleanse\Feast\Classes;

use Log;
use Queue;
use Cleanse\PvPaissa\Classes\UpdateOrCreatePlayer;
use Cleanse\PvPaissa\Classes\HelperRankSort;
use Cleanse\Feast\Models\FeastParty;
use Cleanse\Feast\Models\FeastPartyDaily;
use Cleanse\Feast\Classes\FeastCrawler;

class PartyRankingsUpdate
{
    public function updateDay($data)
    {
        $list = new FeastCrawler($data['season'], $data['type'], $data['datacenter'], $data['tier'], $data['day']);

        $players = $list->crawl();

        if (empty($players)) {
            return;
        }

        foreach ($players as $player) {
            $solo = new UpdateOrCreatePlayer('party', $player);
            $solo->update($data['season']);
        }
    }

    public function dailyPlayerSort($data)
    {
        $players = FeastPartyDaily::where('day', $data['day'])->get(['id', 'rating']);
        $players = $players->toArray();

        if (!empty($players)) {
            $sort = new HelperRankSort;
            $updatedPlayers = $sort->sortRanks($players, 'rating');

            FeastPartyDaily::where('day', $data['day'])
                ->orderBy('rating', 'desc')
                ->chunk(200, function ($players) use ($updatedPlayers) {
                    foreach ($players as $player) {
                        if (isset($updatedPlayers[$player->id]) && !empty($updatedPlayers[$player->id])) {
                            $player->rank = $updatedPlayers[$player->id];

                            $player->save();
                        }
                    }
                });
        }

        Log::info('Daily party done. ' . $data['day']);
        $typeSeason = ['season' => $data['season'], 'type' => 'party'];
        Queue::push('\Cleanse\Feast\Classes\Jobs\FeastOutdated', $typeSeason);
        Queue::push('\Cleanse\Feast\Classes\Jobs\RankFeastSeason', $typeSeason);
    }

    public function seasonPlayerSort($data)
    {
        $players = FeastParty::where('season', $data['season'])->get(['id', 'rating']);
        $players = $players->toArray();

        if (!empty($players)) {
            $sort = new HelperRankSort;
            $updatedPlayers = $sort->sortRanks($players, 'rating');

            FeastParty::where('season', $data['season'])
                ->orderBy('rating', 'desc')
                ->chunk(200, function ($players) use ($updatedPlayers) {
                    foreach ($players as $player) {
                        if (isset($updatedPlayers[$player->id]) && !empty($updatedPlayers[$player->id])) {
                            $player->rank = $updatedPlayers[$player->id];

                            $player->save();
                        }
                    }
                });
        }

        Log::info('Feast party overall done.');
    }
}
