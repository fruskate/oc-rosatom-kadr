<?php namespace Frukt\Kadr\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateFruktKadrSpecialists2 extends Migration
{
    public function up()
    {
        Schema::table('frukt_kadr_specialists', function($table)
        {
            $table->decimal('salary', 10, 2)->default(0);
        });
    }
    
    public function down()
    {
        Schema::table('frukt_kadr_specialists', function($table)
        {
            $table->dropColumn('salary');
        });
    }
}
