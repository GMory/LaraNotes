<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLaranotesTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('laranotes', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('noting_id')->nullable();
            $table->string('noting_type')->nullable();
            $table->integer('regarding_id')->nullable();
            $table->string('regarding_type')->nullable();
            $table->string('content');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::drop('laranotes');
    }
}