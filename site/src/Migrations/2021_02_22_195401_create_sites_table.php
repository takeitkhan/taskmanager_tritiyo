<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSitesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sites', function (Blueprint $table) {
            $table->id();
            $table->integer('project_id');
            $table->integer('user_id')->nullable();
            $table->text('location')->nullable();
            $table->string('site_code')->nullable();
            $table->string('material')->nullable();
            $table->string('site_head')->nullable();
            $table->string('budget')->nullable();
            $table->enum('completion_status', ['Running', 'Rejected', 'Completed'])->nullable();
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
        Schema::dropIfExists('sites');
    }
}
