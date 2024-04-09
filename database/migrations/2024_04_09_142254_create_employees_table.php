<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmployeesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->increments('id');
            
            $table->integer('department_id')->nullable()->unsigned()->index();
            $table->foreign('department_id')->references('id')->on('departments')->onDelete('cascade');

            $table->integer('position_id')->nullable()->unsigned()->index();
            $table->foreign('position_id')->references('id')->on('positions')->onDelete('cascade');

            $table->string('ecode', 100)->charset('utf8')->unique();
            $table->enum('prefix', ['F', 'M', 'T'])->charset('utf8')->default('F');
            $table->string('fname', 250)->charset('utf8')->nullable();
            $table->string('lname', 250)->charset('utf8')->nullable();
            $table->string('nickname', 250)->charset('utf8')->nullable();
            $table->string('phone', 100)->charset('utf8')->nullable();

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
        Schema::dropIfExists('employees');
    }
}
