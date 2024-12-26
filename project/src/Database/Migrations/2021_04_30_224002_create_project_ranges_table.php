<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProjectRangesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('project_ranges', function (Blueprint $table) {
            $table->id();
            $table->integer('project_id')->nullable();
            $table->date('start')->nullable();
            $table->date('end')->nullable();
            $table->string('project_status')->nullable();
            $table->string('status_key')->nullable();
            $table->integer('is_active')->default(1);
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
        Schema::dropIfExists('project_ranges');
    }
}
