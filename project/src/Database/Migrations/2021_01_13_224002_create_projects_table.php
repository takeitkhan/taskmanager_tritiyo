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
            $table->id();
            $table->string('name')->nullable();
            $table->string('code')->nullable();
            $table->string('type')->nullable();
            $table->string('manager')->nullable();
            $table->string('customer')->nullable();
            $table->mediumText('address')->nullable();
            $table->string('vendor')->nullable();
            $table->string('supplier')->nullable();
            $table->string('location')->nullable();
            $table->string('office')->nullable();
            $table->date('start')->nullable();
            $table->date('end')->nullable();
            $table->string('budget')->nullable();
            $table->string('summary')->nullable();
            $table->json('budget_history')->nullable();
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
        Schema::dropIfExists('projects');
    }
}
