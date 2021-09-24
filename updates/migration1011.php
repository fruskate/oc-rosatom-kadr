<?php namespace Frukt\Kadr\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class Migration1011 extends Migration
{
    public function up()
    {
        Schema::create('frukt_kadr_sp_cond', function($table)
        {
            $table->integer('specialist_id')->unsigned();
            $table->integer('condition_id')->unsigned();
            $table->primary(['specialist_id', 'condition_id']);
        });
    }

    public function down()
    {
        Schema::drop('frukt_kadr_sp_cond');
    }
}