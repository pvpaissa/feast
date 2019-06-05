<?php

namespace Cleanse\Feast\Classes;

use Carbon\Carbon;
use Cleanse\Feast\Models\FeastSolo;
use Cleanse\Feast\Models\FeastParty;
use Cleanse\Feast\Models\Party;

class RankingsOutdated
{
    public $soloOffset = 8675309;
    public $partyOffset = 8675309;

    public function updateSolo($data)
    {
        $formatted_date = Carbon::now()->subHours(1)->toDateTimeString();
        $oldPlayers = FeastSolo::where('updated_at', '<' , $formatted_date)
            ->where('season', $data['season'])
            ->where('old', 0)
            ->orderBy('rating', 'desc')
            ->get();

        foreach ($oldPlayers as $player) {
            $player->old = 1;
            $player->change = '0';
            $player->rank = $this->soloOffset;

            $player->save();
        }
    }

    public function updateParty($data)
    {
        $formatted_date = Carbon::now()->subHours(1)->toDateTimeString();
        $oldPlayers = FeastParty::where('updated_at', '<' , $formatted_date)
            ->where('season', $data['season'])
            ->where('old', 0)
            ->orderBy('rating', 'desc')
            ->get();

        foreach ($oldPlayers as $player) {
            $player->old = 1;
            $player->change = '0';
            $player->rank = $this->partyOffset;

            $player->save();
        }
    }

    public function updateLightParty($data)
    {
        $formatted_date = Carbon::now()->subHours(1)->toDateTimeString();
        $oldParties = Party::where('updated_at', '<' , $formatted_date)
            ->where('season', $data['season'])
            ->where('old', 0)
            ->orderBy('rating', 'desc')
            ->get();

        foreach ($oldParties as $party) {
            $party->old = 1;
            $party->rank = $this->partyOffset;

            $party->save();
        }
    }
}
