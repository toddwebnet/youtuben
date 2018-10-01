<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Videos extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('videos', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('channel_id');
            $table->string('youtube_video_id');
            $table->string('duration');
            $table->string('title');
            $table->text('descr');
            $table->string('default_img_url');
            $table->string('medium_img_url');
            $table->string('high_img_url');
            $table->text('player_html');
            $table->datetime('published_at');
            $table->timestamps();
        });


        Schema::create('video_tags', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('video_id');
            $table->string('tag');
            $table->timestamps();

            $table->foreign('video_id')->references('id')->on('videos');
        });

        Schema::create('video_statistics', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('download_id');
            $table->unsignedInteger('video_id');
            $table->unsignedBigInteger('views');
            $table->unsignedBigInteger('likes');
            $table->unsignedBigInteger('dislikes');
            $table->unsignedBigInteger('favorites');
            $table->unsignedBigInteger('comment_count');
            $table->boolean('latest')->nullable();
            $table->timestamps();
            $table->foreign('video_id')->references('id')->on('videos');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('video_statistics');
        Schema::dropIfExists('video_tags');
        Schema::dropIfExists('videos');
    }
}
