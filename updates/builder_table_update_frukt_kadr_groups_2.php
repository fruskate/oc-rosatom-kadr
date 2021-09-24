<?php namespace Frukt\Kadr\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateFruktKadrGroups2 extends Migration
{
    public function up()
    {
        Schema::table('frukt_kadr_groups', function($table)
        {
            $table->string('code')->nullable();
        });
    }
    
    public function down()
    {
        Schema::table('frukt_kadr_groups', function($table)
        {
            $table->dropColumn('code');
        });
    }
}
