<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTaskSiteCompleteTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('task_site_complete', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id')->nullable();
            $table->integer('task_id')->nullable();
            $table->integer('site_id')->nullable();
            $table->string('task_for')->nullable();
            $table->string('status')->nullable();
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
        Schema::dropIfExists('task_site_complete');
    }
}
