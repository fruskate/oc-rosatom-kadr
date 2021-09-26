<?php namespace Frukt\Kadr\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateFruktKadrSpecialists7 extends Migration
{
    public function up()
    {
        Schema::table('frukt_kadr_specialists', function($table)
        {
            $table->integer('pcnt_week')->nullable();
            $table->integer('pcnt_two_weeks')->nullable();
            $table->integer('pcnt_month')->nullable();
        });
    }
    
    public function down()
    {
        Schema::table('frukt_kadr_specialists', function($table)
        {
            $table->dropColumn('pcnt_week');
            $table->dropColumn('pcnt_two_weeks');
            $table->dropColumn('pcnt_month');
        });
    }
}
