<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInfluencersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('influencers', function (Blueprint $table) {
            $table->increments('id');

            $table->string('fullname', 250)->charset('utf8')->nullable();
            $table->enum('gender', ['ชาย', 'หญิง', 'เพศทางเลือก'])->charset('utf8')->default('หญิง');
            $table->string('email', 100)->charset('utf8')->nullable();
            $table->string('phone', 100)->charset('utf8')->nullable();

            $table->integer('career_id')->nullable()->unsigned()->index();
            $table->foreign('career_id')->references('id')->on('career')->onDelete('cascade');
            
            $table->string('line_id', 250)->charset('utf8')->nullable();
            
            $table->integer('content_style_id')->nullable()->unsigned()->index();
            $table->foreign('content_style_id')->references('id')->on('content_style')->onDelete('cascade');
            
            $table->string('birthday', 250)->charset('utf8')->nullable();
            $table->text('product_address')->charset('utf8')->nullable();
            $table->string('product_province', 100)->charset('utf8')->nullable();
            $table->string('product_district', 100)->charset('utf8')->nullable();
            $table->string('product_subdistrict', 100)->charset('utf8')->nullable();
            $table->integer('product_zip')->nullable();

            $table->integer('bank_id')->nullable();
            $table->text('bank_account')->charset('utf8')->nullable();
            $table->text('bank_brand')->charset('utf8')->nullable();

            $table->string('id_card', 250)->charset('utf8')->nullable();
            $table->string('name_of_card', 250)->charset('utf8')->nullable();
            $table->text('address_of_card')->charset('utf8')->nullable();
            $table->string('influencer_province', 100)->charset('utf8')->nullable();
            $table->string('influencer_district', 100)->charset('utf8')->nullable();
            $table->string('influencer_subdistrict', 100)->charset('utf8')->nullable();
            $table->integer('influencer_zip')->nullable();

            $table->string('image_bank', 255)->nullable()->charset('utf8');
            $table->string('image_card', 255)->nullable()->charset('utf8');

            $table->text('note')->charset('utf8')->nullable();

            $table->text('map')->charset('utf8')->nullable();
            $table->enum('status', ['Yes', 'No', 'Request'])->charset('utf8')->default('Yes');
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
        Schema::dropIfExists('influencers');
    }
}
