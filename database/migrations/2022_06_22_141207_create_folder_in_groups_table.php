<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFolderInGroupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('folder_in_groups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('folder_id')->constrained('folders'); 
            $table->foreignId('group_mail_id')->constrained('group_mails'); 
            $table->integer('to_full')->default(0);
            $table->integer('to_read')->default(0);
            $table->integer('status')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('folder_in_groups');
    }
}
