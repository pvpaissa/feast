<?php

namespace Cleanse\Feast\Classes;

use Carbon\Carbon;
use Cleanse\Feast\Models\FeastParty;
use Cleanse\Feast\Models\FeastPartyDaily;

class UpdateParty
{
    protected $season;
    protected $data;

    public function __construct($season, $data)
    {
        $this->season = $season;
        $this->data = $data;
    }

    public function update($player)
    {
        $this->addMode($player);
        $this->addDaily($player);
    }

    public function addMode($player)
    {
        FeastParty::updateOrCreate(
        [
            'player_id' => $player,
            'season'    => $this->season
        ],
        [
            'player_id'  => $player,
            'rating'     => $this->data['rating'],
            'change'     => $this->data['change'],
            'wins'       => $this->data['wins'],
            'matches'    => $this->data['matches'],
            'percent'    => $this->data['percent'],
            'season'     => $this->season,
            'old'        => 0,
            'updated_at' => Carbon::now()->toDateTimeString()
        ]
    );
    }

    private function addDaily($player)
    {
        FeastPartyDaily::firstOrCreate([
            'player_id' => $player,
            'rating'    => $this->data['rating'],
            'change'    => $this->data['change'],
            'wins'      => $this->data['wins'],
            'matches'   => $this->data['matches'],
            'season'    => $this->season,
            'day'       => $this->data['day']
        ]);
    }
}
