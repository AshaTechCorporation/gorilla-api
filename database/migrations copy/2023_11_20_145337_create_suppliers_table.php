<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSuppliersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('suppliers', function (Blueprint $table) {
            $table->increments('id');
            $table->string('code', 100)->charset('utf8')->nullable();
            $table->text('name')->charset('utf8')->nullable();
            $table->text('tax')->charset('utf8')->nullable();
            $table->string('phone', 100)->charset('utf8')->nullable();
            $table->string('email', 200)->charset('utf8')->nullable();
            $table->text('address')->charset('utf8')->nullable();

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
        Schema::dropIfExists('suppliers');
    }
}
