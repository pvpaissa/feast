<?php

namespace Cleanse\Feast\Components;

use DB;
use Config;
use Cms\Classes\ComponentBase;
use Cleanse\PvPaissa\Models\Player;

/**
 * Incomplete Class
 */
class Stats extends ComponentBase
{
    public $top;
    public $solo;
    public $party;
    public $server;
    public $lpservers;
    public $soloPercent;
    public $partyPercent;

    public function componentDetails()
    {
        return [
            'name' => 'Feast Stats',
            'description' => 'Miscellaneous feast stat comparisons.'
        ];
    }

    public function defineProperties()
    {
        return [
            'type' => [
                'title'       => 'Stat Type',
                'description' => 'Select between: player, server, datacenter',
                'default'     => 'player',
                'type'        => 'string'
            ],
            'mode' => [
                'title'       => 'Mode Type',
                'description' => 'Select between: solo and party',
                'type'        => 'dropdown',
                'default'     => 'solo',
                'placeholder' => 'Select units',
                'options'     => ['solo'=>'Solo', 'party'=>'Light Party']
            ],
            'season' => [
                'title'       => 'Feast Season',
                'description' => 'Narrow it down to a specific season season.',
                'default'     => Config::get('cleanse.feast::season', 1),
                'type'        => 'string',
                'validationPattern' => '^[0-9]+$',
                'validationMessage' => 'Please enter a season number.'
            ]
        ];
    }

    public function onRun()
    {
        $season = Config::get('cleanse.feast::season', 1);
        $this->top = $this->page['top'] = $this->loadSoloServers($season);
    }

    public function getTopSoloPlayers()
    {
        return Player::select(
            'cleanse_pvpaissa_players.name',
            DB::raw('avg(cleanse_feast_solo.rating) AS average'),
            DB::raw('count(cleanse_feast_solo.id) AS seasons')
        )
            ->join('cleanse_feast_solo', 'cleanse_feast_solo.player_id', '=', 'cleanse_pvpaissa_players.id')
            ->groupBy('cleanse_feast_solo.player_id')
            ->orderBy('average', 'desc')
            ->where('data_center', 'Aether')
            ->take(20)
            ->get();
    }

    /**
     * Incomplete Methods
     */
    public function loadSolo()
    {
        return Player::select('cleanse_pvpaissa_players.data_center',
            DB::raw('avg(cleanse_feast_solo.rating) AS average'))
            ->join('cleanse_feast_solo', 'cleanse_feast_solo.player_id', '=', 'cleanse_pvpaissa_players.id')
            ->groupBy('data_center')
            ->where('cleanse_feast_solo.season', 6)
            ->where('cleanse_feast_solo.rank', '!=', 8675309)
            ->get();
    }

    public function loadSoloPercent()
    {
        return Player::select('cleanse_pvpaissa_players.data_center',
            DB::raw('avg(cleanse_feast_solo.percent) AS average'))
            ->join('cleanse_feast_solo', 'cleanse_feast_solo.player_id', '=', 'cleanse_pvpaissa_players.id')
            ->groupBy('data_center')
            ->where('cleanse_feast_solo.season', Config::get('cleanse.pvpaissa::season', 1))
            ->where('cleanse_feast_solo.rank', '!=', 8675309)
            ->get();
    }

    public function loadPartyPercent()
    {
        return Player::select('cleanse_pvpaissa_players.data_center',
            DB::raw('avg(cleanse_feast_party.percent) AS average'))
            ->join('cleanse_feast_party', 'cleanse_feast_party.player_id', '=', 'cleanse_pvpaissa_players.id')
            ->groupBy('data_center')
            ->where('cleanse_feast_party.season', Config::get('cleanse.pvpaissa::season', 1))
            ->where('cleanse_feast_party.rank', '!=', 8675309)
            ->get();
    }

    public function loadParty()
    {
        return Player::select('cleanse_pvpaissa_players.data_center',
            DB::raw('avg(cleanse_feast_party.rating) AS average'))
            ->join('cleanse_feast_party', 'cleanse_feast_party.player_id', '=', 'cleanse_pvpaissa_players.id')
            ->groupBy('data_center')
            ->where('cleanse_feast_party.season', Config::get('cleanse.pvpaissa::season', 1))
            ->where('cleanse_feast_party.rank', '!=', 8675309)
            ->get();
    }

    public function loadSoloServers($season = 1)
    {
        return Player::select('cleanse_pvpaissa_players.server',
            DB::raw('avg(cleanse_feast_solo.rating) AS average'),
            DB::raw('count(cleanse_pvpaissa_players.server) AS count'))
            ->join('cleanse_feast_solo', 'cleanse_feast_solo.player_id', '=', 'cleanse_pvpaissa_players.id')
            ->groupBy('server')
            ->where('cleanse_feast_solo.season', $season)
            ->where('cleanse_feast_solo.rank', '!=', 8675309)
            ->orderBy('average', 'desc')
            ->get();
    }

    public function loadPartyServers()
    {
        return Player::select('cleanse_pvpaissa_players.server',
            DB::raw('avg(cleanse_feast_party.rating) AS average'),
            DB::raw('count(cleanse_pvpaissa_players.server) AS count'))
            ->join('cleanse_feast_party', 'cleanse_feast_party.player_id', '=', 'cleanse_pvpaissa_players.id')
            ->groupBy('server')
            ->where('cleanse_feast_party.season', Config::get('cleanse.pvpaissa::season', 1))
            ->where('cleanse_feast_party.rank', '!=', 8675309)
            ->get();
    }
}
