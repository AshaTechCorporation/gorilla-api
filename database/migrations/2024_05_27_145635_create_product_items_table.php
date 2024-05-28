<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_items', function (Blueprint $table) {
            $table->id();

            $table->integer('product_timeline_id')->unsigned()->index();
            $table->foreign('product_timeline_id')->references('id')->on('product_timelines')->onDelete('cascade');

            $table->integer('product_id')->unsigned()->index();
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');

            $table->string('name', 250)->charset('utf8')->nullable();

            $table->integer('platform_social_id')->unsigned()->index();
            $table->foreign('platform_social_id')->references('id')->on('platform_socials')->onDelete('cascade');
            $table->integer('subscribe')->nullable();

            $table->text('description')->charset('utf8')->nullable();
            $table->integer('qty')->nullable();

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
        Schema::dropIfExists('product_items');
    }
}
