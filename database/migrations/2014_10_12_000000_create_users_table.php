<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(
            'users', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('name', 12)->nullable();
                $table->char('gender',6);
                $table->tinyInteger('classId');
                $table->tinyInteger('lvl')->default(1);
                $table->string('email')->unique();
                $table->timestamp('email_verified_at')->nullable();
                $table->string('password');
                $table->rememberToken();
                $table->dateTime('last_activity')->nullable();
                $table->timestamps();
                $table->smallInteger('stat_points')->default(0);
                $table->unsignedSmallInteger("str")->default("15");
                $table->unsignedSmallInteger("dex")->default("15");
                $table->unsignedSmallInteger("sta")->default("15");
                $table->unsignedSmallInteger("int")->default("15");
                $table->unsignedSmallInteger("attack")->default("1");
                $table->unsignedSmallInteger("critical_chance")->default("1");
                $table->unsignedSmallInteger("defense")->default("1");
                $table->unsignedSmallInteger("block")->default("0");
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
        //Schema::dropIfExists('message');
        Schema::dropIfExists('users');
    }
}
