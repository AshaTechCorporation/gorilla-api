<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSubCategoryProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sub_category_products', function (Blueprint $table) {
            $table->increments('id');

            $table->string('prefix', 100)->charset('utf8')->nullable();

            $table->integer('category_product_id')->unsigned()->index();
            $table->foreign('category_product_id')->references('id')->on('category_products')->onDelete('cascade');

            $table->text('name')->nullable()->charset('utf8');

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
        Schema::dropIfExists('sub_category_products');
    }
}
