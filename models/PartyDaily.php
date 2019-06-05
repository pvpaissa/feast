<?php

namespace Cleanse\Feast\Models;

use Model;

/**
 * Class PartyDaily
 * @package Cleanse\Feast\Models
 *
 * @property integer id
 * @property integer party_id
 * @property integer rating
 * @property integer change
 * @property string  roster
 * @property integer day
 * @property integer division
 * @property integer rank
 */
class PartyDaily extends Model
{
    public $table = 'cleanse_feast_light_party_daily';

    public $fillable = [
        'party_id',
        'rating',
        'change',
        'roster',
        'day',
        'division',
        'rank'
    ];

    protected $jsonable = ['roster'];

    protected $casts = ['roster' => 'array'];

    public $belongsTo = [
        'team' => [
            'Cleanse\Feast\Models\Party',
            'key' => 'party_id'
        ]
    ];
}
