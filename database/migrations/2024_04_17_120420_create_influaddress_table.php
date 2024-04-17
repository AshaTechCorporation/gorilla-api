<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInfluaddressTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('influaddress', function (Blueprint $table) {
            $table->increments('id');

            $table->string('latitude', 250)->charset('utf8')->nullable();
            $table->string('longitude', 250)->charset('utf8')->nullable();
            $table->string('address', 250)->charset('utf8')->nullable();

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
        Schema::dropIfExists('influaddress');
    }
}
