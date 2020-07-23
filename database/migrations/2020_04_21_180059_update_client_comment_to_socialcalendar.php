<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateClientCommentToSocialcalendar extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('social_calendar_posts', function (Blueprint $table) {
            $table->longText('admin_comment')->change();
            $table->longText('client_comment')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('social_calendar_posts', function (Blueprint $table) {
            $table->string('admin_comment')->change();
            $table->string('client_comment')->change();
        });
    }
}
