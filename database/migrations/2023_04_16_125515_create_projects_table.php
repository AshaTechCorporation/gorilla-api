<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProjectsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('customer_id')->nullable()->unsigned()->index();
            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');

            $table->string('name', 250)->charset('utf8')->nullable();
            $table->string('strdate', 250)->charset('utf8')->nullable();
            $table->string('enddate', 250)->charset('utf8')->nullable();
            $table->integer('tax')->nullable();

            $table->enum('status', ['open', 'ongoing', 'closed'])->charset('utf8')->default('closed');
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
        Schema::dropIfExists('projects');
    }
}
