<?php namespace Pensoft\Mailsadministration\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableDeletePensoftMailsadministrationData extends Migration
{
    public function up()
    {
        Schema::dropIfExists('pensoft_mailsadministration_data');
    }
    
    public function down()
    {
        Schema::create('pensoft_mailsadministration_data', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->timestamp('deleted_at')->nullable();
            $table->string('email', 255);
            $table->string('type', 255)->default('goto');
            $table->integer('group_id');
        });
    }
}
