<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmployeeCredentialsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('employee_credentials', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('employee_id')->unsigned()->nullable()->index();
            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade');
            $table->string('UID', 250)->charset('utf8')->nullable();
            $table->string('LCID', 250)->charset('utf8')->nullable();
            
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
        Schema::table('employee_credentials', function (Blueprint $table) {
            // Drop the foreign key constraint
            $table->dropForeign(['employee_id']);
        });
        Schema::dropIfExists('employee_credentials');
    }
}
