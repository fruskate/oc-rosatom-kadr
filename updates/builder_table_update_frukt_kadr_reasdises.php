<?php namespace Frukt\Kadr\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateFruktKadrReasdises extends Migration
{
    public function up()
    {
        Schema::table('frukt_kadr_reasdises', function($table)
        {
            $table->string('comment')->nullable();
        });
    }
    
    public function down()
    {
        Schema::table('frukt_kadr_reasdises', function($table)
        {
            $table->dropColumn('comment');
        });
    }
}
