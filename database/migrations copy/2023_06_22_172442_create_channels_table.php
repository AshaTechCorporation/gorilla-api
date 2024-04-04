<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChannelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('channels', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('area_id')->nullable();

            $table->integer('shelve_id')->unsigned()->index();
            $table->foreign('shelve_id')->references('id')->on('shelves')->onDelete('cascade');

            $table->integer('floor_id')->unsigned()->index();
            $table->foreign('floor_id')->references('id')->on('floors')->onDelete('cascade');

            $table->string('name', 250)->charset('utf8')->nullable();
            $table->text('detail')->charset('utf8')->nullable();

            $table->string('create_by', 100)->charset('utf8')->nullable();
            $table->string('update_by', 100)->charset('utf8')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('channels');
    }
}
