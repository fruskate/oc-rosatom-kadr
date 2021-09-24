<?php namespace Frukt\Kadr\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class Migration109 extends Migration
{
    public function up()
    {
        Schema::create('frukt_kadr_specialist_group', function($table)
        {
            $table->integer('specialist_id')->unsigned();
            $table->integer('group_id')->unsigned();
            $table->primary(['specialist_id', 'group_id']);
            $table->string('value')->nullable();
        });
    }

    public function down()
    {
        Schema::drop('frukt_kadr_specialist_group');
    }
}