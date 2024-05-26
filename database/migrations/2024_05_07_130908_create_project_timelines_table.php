<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProjectTimelinesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('project_timelines', function (Blueprint $table) {
            $table->increments('id');

            // Year INT NOT NULL,
            // Month INT NOT NULL,

            $table->integer('project_id')->unsigned()->index();
            $table->foreign('project_id')->references('id')->on('projects')->onDelete('cascade');

            $table->integer('influencer_id')->unsigned()->index();
            $table->foreign('influencer_id')->references('id')->on('influencers')->onDelete('cascade');

            $table->text('draft_link_1')->charset('utf8')->nullable();
            $table->text('client_feedback_1')->charset('utf8')->nullable();
            $table->text('admin_feedback_1')->charset('utf8')->nullable();
            $table->enum('status_1', ['approve', 'reject', 'waiting'])->charset('utf8')->default('waiting');

            $table->text('draft_link_2')->charset('utf8')->nullable();
            $table->text('client_feedback_2')->charset('utf8')->nullable();
            $table->text('admin_feedback_2')->charset('utf8')->nullable();
            $table->enum('status_2', ['approve', 'reject', 'waiting'])->charset('utf8')->default('waiting');

            $table->enum('draft_status', ['TRUE', 'FALSE', 'WAIT'])->charset('utf8')->default('WAIT');

            $table->date('post_date')->nullable();
            $table->enum('post_status', ['TRUE', 'FALSE', 'WAIT'])->charset('utf8')->default('WAIT');
            $table->text('post_link')->charset('utf8')->nullable();
            $table->text('post_code')->charset('utf8')->nullable();

            $table->integer('stat_view')->nullable();
            $table->integer('stat_like')->nullable();
            $table->integer('stat_comment')->nullable();
            $table->integer('stat_share')->nullable();

            $table->text('remark')->charset('utf8')->nullable();
            
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
        Schema::dropIfExists('project_timelines');
    }
}
