<?php

namespace Cleanse\Feast\Classes;

use Carbon\Carbon;
use Cleanse\Feast\Models\FeastSolo;
use Cleanse\Feast\Models\FeastSoloDaily;

class UpdateSolo
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

    private function addMode($player)
    {
        FeastSolo::updateOrCreate(
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
        FeastSoloDaily::firstOrCreate([
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
