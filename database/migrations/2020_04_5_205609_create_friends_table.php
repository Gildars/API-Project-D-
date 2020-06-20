<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFriendsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(
            'friends', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->uuid('id_friend_one');
                $table->uuid('id_friend_two');
                $table->foreign('id_friend_one')->references('id')->on('characters')->onDelete('restrict');
                $table->foreign('id_friend_two')->references('id')->on('characters')->onDelete('restrict');
        }
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('friends');
    }
}
