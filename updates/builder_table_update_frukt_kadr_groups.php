<?php namespace Frukt\Kadr\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateFruktKadrGroups extends Migration
{
    public function up()
    {
        Schema::table('frukt_kadr_groups', function($table)
        {
            $table->text('options')->nullable();
        });
    }
    
    public function down()
    {
        Schema::table('frukt_kadr_groups', function($table)
        {
            $table->dropColumn('options');
        });
    }
}
