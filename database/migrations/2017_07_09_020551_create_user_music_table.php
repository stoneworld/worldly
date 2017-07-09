<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserMusicTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_music',function(Blueprint $table){
            $table->increments('id');
            $table->integer('user_id')->comment('用户ID')->index();
            $table->integer('music_id')->comment('歌曲ID');
            $table->string('music_name')->nullable()->comment('歌曲名称')->index();
            $table->string('singer')->nullable()->comment('歌手姓名');
            $table->string('music_url')->nullable()->comment('歌曲URL');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('user_music');
    }
}
