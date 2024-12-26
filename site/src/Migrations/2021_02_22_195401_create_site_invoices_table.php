<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSiteInvoicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('site_invoices', function (Blueprint $table) {
            $table->id();
            $table->integer('site_id');
            $table->integer('project_id');
            $table->string('invoice_no')->nullable();
            $table->string('invoice_amount')->nullable();
            $table->date('invoice_date')->nullable();
            $table->enum('type', ['Partial', 'Full'])->nullable();
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
        Schema::dropIfExists('site_invoices');
    }
}
