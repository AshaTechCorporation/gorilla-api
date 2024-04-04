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
            $table->enum('gender', ['F', 'M', 'T'])->charset('utf8')->default('F');
            $table->string('email', 100)->charset('utf8')->nullable();
            $table->string('phone', 100)->charset('utf8')->nullable();
            $table->string('occupation', 250)->charset('utf8')->nullable();
            $table->string('line_id', 250)->charset('utf8')->nullable();
            $table->string('content_style', 250)->charset('utf8')->nullable();
            $table->string('birthday', 250)->charset('utf8')->nullable();
            $table->text('address')->charset('utf8')->nullable();
            $table->integer('province_id')->nullable();

            $table->integer('bank_id')->nullable();
            $table->text('bank_account')->charset('utf8')->nullable();
            $table->text('bank_number')->charset('utf8')->nullable();

            $table->string('id_card', 250)->charset('utf8')->nullable();
            $table->string('name_of_card', 250)->charset('utf8')->nullable();
            $table->text('address_of_card')->charset('utf8')->nullable();

            $table->string('image_profile', 255)->nullable()->charset('utf8');
            $table->string('image_bank', 255)->nullable()->charset('utf8');
            $table->string('image_card', 255)->nullable()->charset('utf8');

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
