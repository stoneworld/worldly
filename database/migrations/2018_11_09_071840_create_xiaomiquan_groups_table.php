<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateXiaomiquanGroupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('xiaomiquan_groups', function (Blueprint $table) {
            $table->unsignedBigInteger('id', true);
            $table->unsignedBigInteger('owner_user_id')->index()->default(0);
            $table->string('owner_user_name')->default('');
            $table->unsignedInteger('number')->default('0')->comment('数量');
            $table->string('name', 255)->default('')->comment('name');
            $table->string('description', 255)->default('')->comment('描述');
            $table->timestamp('create_time')->nullable()->comment('圈子创建时间');
            $table->string('type')->default('pay')->comment('类型');
            $table->string('background_url')->default('')->comment('背景图');
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
        Schema::dropIfExists('xiaomiquan_groups');
    }
}
