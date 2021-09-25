<?php namespace Frukt\Kadr\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableCreateFruktKadrFamilies extends Migration
{
    public function up()
    {
        Schema::create('frukt_kadr_families', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id')->unsigned();
            $table->string('name')->nullable();
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('frukt_kadr_families');
    }
}
