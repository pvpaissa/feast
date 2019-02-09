<?php

namespace Cleanse\Feast\Models;

use Model;

class FeastParty extends Model
{
    public $table = 'cleanse_feast_party';

    public $fillable = [
        'player_id',
        'rating',
        'change',
        'wins',
        'matches',
        'percent',
        'season',
        'old',
        'updated_at'
    ];

    public $hasOne = [
        'player' => [
            'Cleanse\Pvpaissa\Models\Player',
            'key' => 'id',
            'otherKey' => 'player_id'
        ]
    ];

    public $belongsTo = [
        'player' => [
            'Cleanse\Pvpaissa\Models\Player',
            'key' => 'player_id'
        ]
    ];
}
