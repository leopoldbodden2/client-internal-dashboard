<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCdyneMessagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cdyne_messages', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedBigInteger('user_id');
            $table->string('phone');
            $table->string('name')->nullable();
            $table->string('subject');
            $table->text('body');
            $table->string('message_id')->nullable();
            $table->string('referenceid')->nullable();
            $table->boolean('attempted')->default(0);
            $table->boolean('successful')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cdyne_messages');
    }
}
