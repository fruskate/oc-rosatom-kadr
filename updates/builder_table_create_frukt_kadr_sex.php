<?php namespace Frukt\Kadr\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableCreateFruktKadrSex extends Migration
{
    public function up()
    {
        Schema::create('frukt_kadr_sex', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id')->unsigned();
            $table->string('name')->nullable();
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('frukt_kadr_sex');
    }
}
