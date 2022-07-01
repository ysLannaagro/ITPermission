<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGroupMailRelationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('group_mail_relations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('group_mail_main')->constrained('group_mails'); 
            $table->foreignId('group_mail_detail')->nullable()->constrained('group_mails');             
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
        Schema::dropIfExists('group_mail_relations');
    }
}
