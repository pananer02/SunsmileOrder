<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();            
            $table->string('IDCustomer')->nullable();
            $table->string('CustomerName');
            $table->string("Day_OrderDate");
            $table->string("OrderDate");
            $table->string("Day_ShipDate");
            $table->string("ShipDate");
            $table->string("SubmitDate")->nullable();
            $table->string("DepotRoute")->nullable();
            $table->string("EmployeeRoute")->nullable();
            $table->string("Schedule")->nullable();
            $table->string("Status");
            $table->string("CreateBy");
            $table->string("UpdateBy");
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
        Schema::dropIfExists('orders');
    }
};
