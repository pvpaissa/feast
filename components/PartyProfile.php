<?php

namespace Cleanse\Feast\Components;

use Cms\Classes\ComponentBase;
use Cleanse\Feast\Models\Party;

class PartyProfile extends ComponentBase
{
    public $lp;
    public $season;

    public function componentDetails()
    {
        return [
            'name'          => 'FFXIV Feast Light Party team profile.',
            'description'   => 'Grabs the Team\'s Feast daily stats.'
        ];
    }

    public function defineProperties()
    {
        return [
            'lodestone' => [
                'title'       => 'Team Lodestone Slug',
                'description' => 'Look up the team by their id.',
                'default'     => '{{ :lodestone }}',
                'type'        => 'string'
            ],
            'season' => [
                'title'       => 'Feast Season',
                'description' => 'Look up the season\'s stats.',
                'default'     => '{{ :season }}',
                'type'        => 'string',
                'validationPattern' => '^[0-9]+$',
                'validationMessage' => 'Please enter a proper season number.'
            ]
        ];
    }

    public function onRun()
    {
        $this->season = $this->page['season'] = $this->property('season') ?: 1;
        $this->lp = $this->page['lp'] = $this->loadStats();

        $this->addCss('assets/css/feast.css');
    }

    public function loadStats()
    {
        $lodestone = $this->property('lodestone');

        return Party::with([
                'daily' => function($q) {
                    $q->orderBy('created_at', 'desc');
                }
            ])
            ->where([
                'lodestone' => $lodestone,
                'season' => $this->season
            ])
            ->first();
    }
}
