<?php

namespace Cleanse\Feast\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class AddLightPartyTables extends Migration
{
    public function up()
    {
        Schema::create('cleanse_feast_light_party', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->string('name');
            $table->string('dc_group');
            $table->integer('season')->unsigned()->nullable();
            $table->string('lodestone');
            $table->integer('rating')->unsigned()->nullable();
            $table->json('crest')->nullable();
            $table->integer('rank')->unsigned()->nullable();
            $table->smallInteger('old')->default(0);
            $table->timestamps();
        });

        Schema::create('cleanse_feast_light_party_daily', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->integer('party_id')->unsigned()->nullable();
            $table->integer('rating')->unsigned()->nullable();
            $table->string('change')->nullable();
            $table->json('roster')->nullable();
            $table->string('division')->nullable();
            $table->integer('day')->unsigned()->nullable();
            $table->integer('rank')->unsigned()->nullable();
            $table->timestamps();
        });
    }
}
