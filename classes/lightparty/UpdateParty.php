<?php

namespace Cleanse\Feast\Classes\LightParty;

use Carbon\Carbon;
use Cleanse\Feast\Models\Party;
use Cleanse\Feast\Models\PartyDaily;

class UpdateParty
{
    protected $season;
    protected $data;

    public function __construct($season, $data)
    {
        $this->season = $season;
        $this->data = $data;
    }

    public function update()
    {
        $lpId = $this->addMode();
        $this->addDaily($lpId);
    }

    private function addMode()
    {
        $lightParty = Party::updateOrCreate(
            [
                'lodestone' => $this->data['lodestone'],
                'season'    => $this->season
            ],
            [
                'name'       => $this->data['name'],
                'dc_group'   => $this->data['dc_group'],
                'season'     => $this->season,
                'lodestone'  => $this->data['lodestone'],
                'rating'     => $this->data['rating'],
                'crest'      => $this->data['crest'],
                'old'        => 0,
                'updated_at' => Carbon::now()->toDateTimeString()
            ]
        );

        return $lightParty->id;
    }

    private function addDaily($id)
    {
        PartyDaily::firstOrCreate([
            'party_id' => $id,
            'rating'   => $this->data['rating'],
            'change'   => $this->data['change'],
            'roster'   => $this->data['roster'],
            'day'      => $this->data['day'],
            'division' => $this->data['division']
        ]);
    }
}
