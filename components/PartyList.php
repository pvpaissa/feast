<?php

namespace Cleanse\Feast\Components;

use Config;
use ValidationException;
use Cms\Classes\ComponentBase;
use Cleanse\Feast\Models\Party;

class PartyList extends ComponentBase
{
    public $rankings;
    public $season;
    public $amount;

    public function componentDetails()
    {
        return [
            'name'          => 'Overall Light Party Team Feast Rankings',
            'description'   => 'Grabs the rankings for feast party players.'
        ];
    }

    public function defineProperties()
    {
        return [
            'season' => [
                'title'       => 'Light Party Season',
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
        $this->season = $this->page['season'] = 1;
        $this->rankings = $this->page['rankings'] = $this->loadRankings();

        $this->addCss('assets/css/feast.css');
    }

    private function loadRankings()
    {
        return Party::with(['latestRecorded'])->where('season', $this->season)
            ->orderBy('rating', 'desc')
            ->paginate(100);
    }
}
