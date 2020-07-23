<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSocialCalendarPostsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('social_calendar_posts', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('calendar_id')->unsigned();

            $table->date('publish_date')->nullable();
            $table->string('post_title')->nullable();
            $table->string('post_url')->nullable();
            $table->string('post_publisher')->nullable();
            $table->string('post_topic')->nullable();
            $table->string('client_comment')->nullable();

            $table->boolean('user_approved')->nullable();

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
        Schema::dropIfExists('social_calendar_posts');
    }
}
