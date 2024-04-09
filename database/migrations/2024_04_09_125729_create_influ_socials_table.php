<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInfluSocialsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('influ_socials', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('influencer_id')->unsigned()->index();
            $table->foreign('influencer_id')->references('id')->on('influencers')->onDelete('cascade');
            $table->integer('platform_social_id')->unsigned()->index();
            $table->foreign('platform_social_id')->references('id')->on('platform_socials')->onDelete('cascade');
            $table->integer('Subscribe')->nullable();

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
        Schema::dropIfExists('influ_socials');
    }
}
