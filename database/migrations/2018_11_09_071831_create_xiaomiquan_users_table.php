<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateXiaomiquanUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('xiaomiquan_users', function (Blueprint $table) {
            $table->unsignedBigInteger('id', true);
            $table->string('name', 255)->default('')->comment('name');
            $table->string('avatar_url')->default('')->comment('背景图');
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
        Schema::dropIfExists('xiaomiquan_users');
    }
}
