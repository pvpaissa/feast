<?php

namespace Cleanse\Feast\Classes;

use Log;
use Queue;
use Cleanse\PvPaissa\Classes\UpdateOrCreatePlayer;
use Cleanse\PvPaissa\Classes\HelperRankSort;
use Cleanse\Feast\Models\FeastSolo;
use Cleanse\Feast\Models\FeastSoloDaily;
use Cleanse\Feast\Classes\FeastCrawler;

class SoloRankingsUpdate
{
    public function updateDay($data)
    {
        $list = new FeastCrawler($data['season'], $data['type'], $data['datacenter'], $data['tier'], $data['day']);

        $players = $list->crawl();

        if (empty($players)) {
            Log::info('Day empty: '.$data['day']);
            return;
        }

        foreach ($players as $player) {
            $solo = new UpdateOrCreatePlayer('solo', $player);
            $solo->update($data['season']);
        }
    }

    public function dailyPlayerSort($data)
    {
        $players = FeastSoloDaily::where('day', $data['day'])->get(['id', 'rating']);
        $players = $players->toArray();

        if (!empty($players)) {
            $sort = new HelperRankSort;
            $updatedPlayers = $sort->sortRanks($players, 'rating');

            FeastSoloDaily::where('day', $data['day'])
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

        Log::info('Daily solo done. ' . $data['day']);
        $typeSeason = ['season' => $data['season'], 'type' => 'solo'];
        Queue::push('\Cleanse\Pvpaissa\Classes\Jobs\FeastOutdated', $typeSeason);
        Queue::push('\Cleanse\Pvpaissa\Classes\Jobs\RankFeastSeason', $typeSeason);
    }

    public function seasonPlayerSort($data)
    {
        $players = FeastSolo::where('season', $data['season'])->get(['id', 'rating']);
        $players = $players->toArray();

        if (!empty($players)) {
            $sort = new HelperRankSort;
            $updatedPlayers = $sort->sortRanks($players, 'rating');

            FeastSolo::where('season', $data['season'])
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

        Log::info('Feast solo overall done.');
    }
}
