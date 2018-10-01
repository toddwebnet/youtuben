<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Channels extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('channels', function (Blueprint $table) {
            $table->increments('id');
            $table->string('youtube_channel_id');
            $table->string('title');
            $table->text('description');
            $table->string('custom_url')->nullable();
            $table->dateTime('published_at');
            $table->string('default_img_url');
            $table->string('medium_img_url');
            $table->string('high_img_url');

            $table->timestamps();
            $table->index(['youtube_channel_id']);
        });

        Schema::create('channel_statistics', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('download_id');
            $table->unsignedInteger('channel_id');
            $table->unsignedBigInteger('views');
            $table->unsignedBigInteger('comment_count');
            $table->unsignedBigInteger('subscriber_count');
            $table->unsignedBigInteger('video_count');
            $table->boolean('latest')->nullable();
            $table->timestamps();

            $table->foreign('download_id')->references('id')->on('downloads');
            $table->foreign('channel_id')->references('id')->on('channels');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('channel_statistics');
        Schema::dropIfExists('channels');

    }
}
