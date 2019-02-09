<?php

namespace Cleanse\Feast\Components;

use Config;
use ValidationException;
use Cms\Classes\ComponentBase;
use Cleanse\Feast\Models\FeastSolo;
use Cleanse\Feast\Models\FeastSoloDaily as Daily;

class Solo extends ComponentBase
{
    public $rankings;
    public $season;
    public $amount;

    public function componentDetails()
    {
        return [
            'name' => 'Overall Solo Feast Rankings',
            'description' => 'Grabs the rankings for feast solo players.'
        ];
    }

    public function onRun()
    {
        $this->season = $this->page['season'] = Config::get('cleanse.feast::season', 1);
        $this->amount = Config::get('cleanse.feast::solo_full_take', 50);

        $this->rankings = $this->page['rankings'] = $this->loadRankings();
    }

    public function loadRankings()
    {
        if (isset($_GET['dc'])) {
            return $this->getDataCenter($_GET['dc']);
        }

        return FeastSolo::where('season', $this->season)
            ->orderBy('rating', 'desc')
            ->paginate($this->amount);
    }

    public function onPlayer()
    {
        $player = post('player', 'Balmung');

        if (!$player) {
            throw new ValidationException(['name' => 'You must enter at least part of a player\'s name!']);
        }

        $player = FeastSolo::whereHas('player', function ($q) use ($player) {
            $q->where('name', 'like', '%' . $player . '%');
        })
            ->where('season', $this->season)
            ->orderBy('rating', 'desc')
            ->get();

        $this->page['items'] = $player;
    }

    public function getDataCenter($dc)
    {
        return FeastSolo::whereHas('player', function ($q) use ($dc) {
            $q->where('data_center', '=', $dc);
        })
            ->whereSeason($this->season)
            ->orderBy('rating', 'desc')
            ->paginate($this->amount);
    }

    public function onDataCenter()
    {
        $dc = post('dc');
        if ($dc == '') {
            $dc = 'Aether';
        }

        $date = post('date');
        if ($date == '') {
            $date = '2017328';
        }

        $dcData = Daily::whereHas('player', function ($q) use ($dc) {
            $q->where('data_center', '=', $dc);
        })
            ->where('day', '=', $date)
            ->whereSeason($this->season)
            ->orderBy('rating', 'desc')
            ->get();

        $this->page['items'] = $dcData;
    }
}
