<?php

namespace Cleanse\Feast\Components;

use Config;
use ValidationException;
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
            'name'          => 'Overall Party Feast Rankings',
            'description'   => 'Grabs the rankings for feast party players.'
        ];
    }

    public function defineProperties()
    {
        return [
            'season' => [
                'title'       => 'Feast Season',
                'description' => 'If you wish to specify a specific season.',
                'default'     => '{{ :season }}',
                'type'        => 'string',
                'validationPattern' => '^[0-9]+$',
                'validationMessage' => 'Please enter a season number.'
            ]
        ];
    }

    public function onRun()
    {
        $this->amount = Config::get('cleanse.feast::party_full_take', 50);

        $this->season = $this->page['season'] = $this->property('season') ?: 4;

        $this->rankings = $this->page['rankings'] = $this->loadRankings();
    }

    private function loadRankings()
    {
        return FeastParty::where('season', $this->season)
            ->orderBy('rating', 'desc')
            ->paginate($this->amount);
    }
}
