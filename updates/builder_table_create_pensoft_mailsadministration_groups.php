<?php namespace Pensoft\Mailsadministration\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableCreatePensoftMailsadministrationGroups extends Migration
{
    public function up()
    {
        Schema::create('pensoft_mailsadministration_groups', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id')->unsigned();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->timestamp('deleted_at')->nullable();
            $table->string('address');
            $table->text('goto');
            $table->text('all_moderators')->nullable();
            $table->text('group_moderators')->nullable();
            $table->smallInteger('use_moderators_type')->default(1);
            $table->string('accesspolicy', 30);
            $table->string('domain');
            $table->smallInteger('active')->default(1);
            $table->string('reply_to')->nullable();
            $table->string('replace_from')->nullable();
            $table->string('replace_to')->nullable();
            $table->string('add_reply_to')->nullable();
            $table->string('name_append')->nullable();
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('pensoft_mailsadministration_groups');
    }
}
