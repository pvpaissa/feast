<?php

namespace Cleanse\Feast\Models;

use Model;

class FeastPartyDaily extends Model
{
    public $table = 'cleanse_feast_party_daily';

    public $fillable = [
        'player_id',
        'rating',
        'change',
        'wins',
        'matches',
        'season',
        'day',
        'rank'
    ];

    public $hasOne = [
        'player' => [
            'Cleanse\PvPaissa\Models\Player',
            'key' => 'id',
            'otherKey' => 'player_id'
        ]
    ];

    public $belongsTo = [
        'player' => [
            'Cleanse\PvPaissa\Models\Player',
            'key' => 'player_id'
        ]
    ];
}
