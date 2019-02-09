<?php

namespace Cleanse\Feast\Components;

use Config;
use Cms\Classes\ComponentBase;
use Cleanse\Feast\Models\FeastParty;

class Party extends ComponentBase
{
    public $rankings;
    public $season;
    public $amount;

    public function componentDetails()
    {
        return [
            'name'            => 'Overall Party Feast Rankings',
            'description'     => 'Grabs the rankings for feast party players.'
        ];
    }

    public function onRun()
    {
        $this->season = $this->page['season'] = Config::get('cleanse.feast::season', 1);
        $this->amount = Config::get('cleanse.feast::party_full_take', 50);

        $this->rankings = $this->page['rankings'] = $this->loadRankings();
    }

    public function loadRankings()
    {
        return FeastParty::where('season', $this->season)
            ->orderBy('rating', 'desc')
            ->paginate($this->amount);
    }

    public function onServer()
    {
        $server = post('newItem', 'Balmung');

        if ($server != '') {
            $server = FeastParty::whereHas('player', function($q) use ($server){
                $q->where('name', 'like', '%'.$server.'%');
            })
                ->where('season', $this->season)
                ->orderBy('rating', 'desc')
                ->get();
        }

        $this->page['items'] = $server;
    }
}
