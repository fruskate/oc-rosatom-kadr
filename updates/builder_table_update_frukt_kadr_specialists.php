<?php namespace Frukt\Kadr\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateFruktKadrSpecialists extends Migration
{
    public function up()
    {
        Schema::table('frukt_kadr_specialists', function($table)
        {
            $table->integer('age')->nullable()->unsigned();
        });
    }
    
    public function down()
    {
        Schema::table('frukt_kadr_specialists', function($table)
        {
            $table->dropColumn('age');
        });
    }
}
