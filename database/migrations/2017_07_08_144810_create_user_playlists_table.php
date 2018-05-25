<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserPlaylistsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_playlists',function(Blueprint $table){
            $table->increments('id');
            $table->string('name')->comment('歌单名称');
            $table->string('description')->nullable()->comment('歌单描述');
            $table->integer('user_id')->comment('所属用户ID')->index();
            $table->bigInteger('playlist_id')->comment('歌单ID')->unique();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('user_playlists');
    }
}
