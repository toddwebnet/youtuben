<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Subscribers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('subscribers', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('download_id');
            $table->string('channel_id');
            $table->string('owner_channel_id');
            $table->timestamps();

            $table->foreign('download_id')->references('id')->on('downloads');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('subscribers');
    }
}
