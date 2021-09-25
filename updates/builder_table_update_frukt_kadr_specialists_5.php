<?php namespace Frukt\Kadr\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateFruktKadrSpecialists5 extends Migration
{
    public function up()
    {
        Schema::table('frukt_kadr_specialists', function($table)
        {
            $table->integer('family_id')->nullable();
        });
    }
    
    public function down()
    {
        Schema::table('frukt_kadr_specialists', function($table)
        {
            $table->dropColumn('family_id');
        });
    }
}
