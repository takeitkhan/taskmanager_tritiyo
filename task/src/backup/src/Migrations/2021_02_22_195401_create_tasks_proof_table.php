<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTasksProofTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tasks_proof', function (Blueprint $table) {
            $table->id();
            $table->integer('task_id');
            $table->integer('proof_sent_by')->nullable();
            $table->string('resource_proof')->nullable();
            $table->string('vehicle_proof')->nullable();
            $table->string('material_proof')->nullable();
            $table->string('anonymous_proof')->nullable();
            $table->string('lat_proof')->nullable();
            $table->string('long_proof')->nullable();
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
        Schema::dropIfExists('tasks_proof');
    }
}
