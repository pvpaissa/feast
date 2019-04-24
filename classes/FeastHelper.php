<?php

namespace Cleanse\Feast\Classes;

class FeastHelper
{
    public $tiers = [5, 4, 3, 2, 1];
    public $types = ['solo']; //public $types = ['solo', 'party'];
    
    public $datacenters = [
        'Aether',
        'Chaos',
        'Crystal',
        'Elemental',
        'Gaia',
        'Light',
        'Mana',
        'Primal'
    ];

    public function yearDay()
    {
        return date("Yz");
    }
}
