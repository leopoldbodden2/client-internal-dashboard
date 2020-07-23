<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSocialCalendarsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('social_calendars', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('calendar_id')->unique();
            $table->integer('user_id')->unsigned();

            $table->boolean('approval_email_queued')->default(0);
            $table->boolean('approval_email_sent')->default(0);
            $table->boolean('approval_email_read')->default(0);

            $table->boolean('approved_by_user')->nullable();
            $table->boolean('published')->default(0);
            $table->timestamp('published_at')->nullable();

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
        Schema::dropIfExists('social_calendars');
    }
}
