<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCommandHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('command_histories',function(Blueprint $table){
            $table->increments('id');
            $table->string('signature')->comment('命令签名');
            $table->string('parame')->comment('命令参数');
            $table->string('description')->comment('命令描述');
            $table->dateTime('starttime')->comment('运行时间');
            $table->dateTime('endtime')->comment('运行时间');
            $table->integer('runtime')->comment('运行时间');
            $table->string('runtimestring')->comment('运行时间');
        });
    }
    /**
    * Reverse the migrations.
    *
    * @return void
    */
    public function down()
    {
        Schema::drop('command_histories');
    }
}
