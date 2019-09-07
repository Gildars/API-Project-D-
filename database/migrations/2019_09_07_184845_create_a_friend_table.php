<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAFriendTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('friend', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('id_friend_one');
            $table->bigInteger('id_friend_two');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('friend', function (Blueprint $table) {
            Schema::dropIfExists('friend');
        });
    }
}
