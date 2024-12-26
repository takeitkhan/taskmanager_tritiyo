<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTasksRequisitionBillTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tasks_requisition_bill', function (Blueprint $table) {
            $table->id();
            $table->integer('task_id');
            $table->json('requisition_prepared_by_manager');
            $table->enum('requisition_submitted_by_manager', ['Yes']);
            $table->json('requisition_edited_by_cfo');
            $table->enum('requisition_approved_by_cfo', ['Yes', 'No']);
            $table->json('requisition_edited_by_accountant');
            $table->enum('requisition_approved_by_accountant', ['Yes', 'No']);
            $table->json('bill_prepared_by_resource');
            $table->enum('bill_submitted_by_resource', ['Yes']);
            $table->json('bill_edited_by_manager');
            $table->enum('bill_approved_by_manager', ['Yes']);
            $table->json('bill_edited_by_cfo');
            $table->enum('bill_approved_by_cfo', ['Yes']);
            $table->json('bill_edited_by_accountant');
            $table->enum('bill_approved_by_accountant', ['Yes']);
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
        Schema::dropIfExists('tasks_requisition_bill');
    }
}
