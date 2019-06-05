<?php

namespace Cleanse\Feast\Models;

use Model;

/**
 * Class Party
 * @package Cleanse\Feast\Models
 *
 * @property integer id
 * @property string  name
 * @property string  dc_group
 * @property integer season
 * @property string  lodestone
 * @property integer rating
 * @property string  crest
 * @property integer rank
 * @property integer old
 */
class Party extends Model
{
    public $table = 'cleanse_feast_light_party';

    public $fillable = [
        'name',
        'dc_group',
        'season',
        'lodestone',
        'rating',
        'crest',
        'rank',
        'old',
        'updated_at'
    ];

    protected $jsonable = ['crest'];

    protected $casts = ['crest'  => 'array'];

    public $hasMany = [
        'daily' => [
            'Cleanse\Feast\Models\PartyDaily',
            'key' => 'party_id'
        ]
    ];

    // Category model
    public function latestRecorded() {
        return $this->hasOne(PartyDaily::class)->latest();
    }
}
