<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateXiaomiquanTopicsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('xiaomiquan_topics', function (Blueprint $table) {
            $table->unsignedBigInteger('id', true);
            $table->unsignedBigInteger('group_id')->index()->default(0);
            $table->unsignedBigInteger('from_user_id')->index()->default(0);
            $table->string('from_user_name')->default('')->comment('提出话题人姓名');
            $table->string('type')->default('q&a')->comment('类型');
            $table->text('desc', 255)->default('')->comment('问题标题或者topic_title');
            $table->text('answer_text', 255)->default('')->comment('答案');
            $table->unsignedBigInteger('answer_user_id')->default(0)->comment('回答人id');
            $table->string('answer_user_name')->default('')->comment('回答人名称');
            $table->unsignedInteger('likes_count')->default(0)->comment('喜欢数量');
            $table->unsignedInteger('reading_count')->default(0)->comment('阅读人数');
            $table->timestamp('create_time')->nullable()->comment('topic创建时间');
            $table->unsignedTinyInteger('is_send')->default(0)->comment('发送与否');
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
        Schema::dropIfExists('xiaomiquan_topics');
    }
}
