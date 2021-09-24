<?php namespace Frukt\Kadr\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableCreateFruktKadrHistories extends Migration
{
    public function up()
    {
        Schema::create('frukt_kadr_histories', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id')->unsigned();
            $table->integer('specialist_id')->nullable()->unsigned();
            $table->integer('condition_id')->nullable()->unsigned();
            $table->string('model')->nullable();
            $table->integer('amount')->default(1);
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('frukt_kadr_histories');
    }
}
