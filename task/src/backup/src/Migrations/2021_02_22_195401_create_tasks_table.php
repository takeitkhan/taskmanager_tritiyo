<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTasksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->string('task_name')->nullable();
            $table->string('task_code')->nullable();
            $table->string('task_type');
            $table->integer('project_id');
            $table->integer('site_head');
            $table->text('task_details')->nullable();
            $table->text('anonymous_proof_details')->nullable();
            $table->enum('task_assigned_to_head', ['Yes', 'No'])->nullable();
            $table->date('task_for')->nullable();
            $table->json('manager_override_chunck')->nullable();
            $table->json('override_status', ['Yes', 'No', 'Overriden'])->nullable();
            $table->string('is_active')->default('1');
            $table->json('	manager_override_chunck')->nullable();
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
        Schema::dropIfExists('tasks');
    }
}
