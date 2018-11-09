<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateXiaomiquanRepliesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('xiaomiquan_replies', function (Blueprint $table) {
            $table->unsignedBigInteger('id', true);
            $table->unsignedBigInteger('user_id')->index()->default(0);
            $table->string('user_name')->index()->default('')->comment('名称');
            $table->unsignedBigInteger('topic_id')->index()->default(0);
            $table->string('text', 255)->default('')->comment('描述');
            $table->timestamp('create_time')->nullable()->comment('创建时间');
            $table->unsignedInteger('likes_count')->default(0)->comment('喜欢数量');
            $table->unsignedInteger('rewards_count')->default(0)->comment('rewards_count');
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
        Schema::dropIfExists('xiaomiquan_replies');
    }
}
