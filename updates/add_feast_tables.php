<?php

namespace Cleanse\Feast\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class AddFeastTables extends Migration
{
    public function up()
    {
        Schema::create('cleanse_feast_solo', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->integer('player_id')->unsigned()->index();
            $table->integer('rank')->default(0)->index();
            $table->integer('rating')->default(0);
            $table->string('change')->default(0);
            $table->integer('wins')->default(0);
            $table->integer('matches')->default(0);
            $table->string('percent');
            $table->smallInteger('season')->default(0);
            $table->smallInteger('old')->default(0);
            $table->timestamps();
        });

        Schema::create('cleanse_feast_solo_daily', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->integer('player_id')->unsigned()->index();
            $table->integer('rank')->default(0);
            $table->integer('rating')->default(0)->index();
            $table->string('change')->default(0);
            $table->integer('wins')->default(0);
            $table->integer('matches')->default(0);
            $table->smallInteger('season')->default(0);
            $table->string('day');
            $table->timestamps();
        });

        Schema::create('cleanse_feast_party', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->integer('player_id')->unsigned()->index();
            $table->integer('rank')->default(0);
            $table->integer('rating')->default(0)->index();
            $table->string('change')->default(0);
            $table->integer('wins')->default(0);
            $table->integer('matches')->default(0);
            $table->string('percent');
            $table->smallInteger('season')->default(0);
            $table->smallInteger('old')->default(0);
            $table->timestamps();
        });

        Schema::create('cleanse_feast_party_daily', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->integer('player_id')->unsigned()->index();
            $table->integer('rank')->default(0);
            $table->integer('rating')->default(0)->index();
            $table->string('change')->default(0);
            $table->integer('wins')->default(0);
            $table->integer('matches')->default(0);
            $table->smallInteger('season')->default(0);
            $table->string('day');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::drop('cleanse_feast_solo');
        Schema::drop('cleanse_feast_solo_daily');
        Schema::drop('cleanse_feast_party');
        Schema::drop('cleanse_feast_party_daily');
    }
}