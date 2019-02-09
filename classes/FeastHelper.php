<?php

namespace Cleanse\Feast\Classes;

class FeastHelper
{
    public $tiers = [5, 4, 3, 2, 1];
    public $types = ['solo']; //public $types = ['solo', 'party'];
    
    //Need to add new data centers soonTM
    public $datacenters = [
        'Aether',
        'Chaos',
        'Elemental',
        'Gaia',
        'Mana',
        'Primal'
    ];

    public function yearDay()
    {
        return date("Yz");
    }
}
