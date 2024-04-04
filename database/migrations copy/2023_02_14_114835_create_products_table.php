<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->increments('id');
            $table->string('code', 50)->charset('utf8')->nullable();

            $table->integer('category_product_id')->unsigned()->index();
            $table->foreign('category_product_id')->references('id')->on('category_products')->onDelete('cascade');

            $table->integer('sub_category_product_id')->unsigned()->index();
            $table->foreign('sub_category_product_id')->references('id')->on('sub_category_products')->onDelete('cascade');

            $table->integer('supplier_id')->nullable();

            $table->integer('area_id')->nullable();
            $table->integer('shelve_id')->nullable();
            $table->integer('floor_id')->nullable();
            $table->integer('channel_id')->nullable();

            $table->text('name')->charset('utf8');
            $table->text('detail')->nullable()->charset('utf8');
            $table->integer('qty')->nullable();
            $table->integer('sale_price')->nullable();
            $table->integer('cost')->nullable();

            $table->integer('min')->nullable();
            $table->integer('max')->nullable();


            $table->enum('type', ['Raw', 'Good', 'All'])->charset('utf8')->default('Good');

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
        Schema::dropIfExists('products');
    }
}
