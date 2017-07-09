<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserCommentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_comments',function(Blueprint $table){
            $table->increments('id');
            $table->integer('user_id')->comment('用户ID')->index();
            $table->integer('comment_id')->comment('评论ID')->unique();
            $table->integer('music_id')->comment('歌曲ID');
            $table->integer('liked_count')->comment('评论喜欢数');
            $table->string('content')->nullable()->comment('歌曲名称')->index();
            $table->timestamp('created_at')->nullable();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('user_comments');
    }
}
