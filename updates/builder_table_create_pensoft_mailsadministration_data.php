<?php namespace Pensoft\Mailsadministration\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableCreatePensoftMailsadministrationData extends Migration
{
    public function up()
    {
        Schema::create('pensoft_mailsadministration_data', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id')->unsigned();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->timestamp('deleted_at')->nullable();
            $table->string('email');
            $table->string('type')->default('goto');
            $table->integer('group_id');
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('pensoft_mailsadministration_data');
    }
}
