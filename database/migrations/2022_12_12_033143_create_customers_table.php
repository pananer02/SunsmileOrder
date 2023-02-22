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
        
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('AccountNumber')->nullable();
            $table->string('AccountDescription')->nullable();
            $table->string('CustomerGrade')->nullable();
            $table->string('SalesChannel')->nullable();
            $table->string('Reference')->nullable();
            $table->string('ChannelCode')->nullable();
            $table->string('SubChannelCode')->nullable();
            $table->string('AddressLine1')->nullable();
            $table->string('AddressLine2')->nullable();
            $table->string('AddressLine3')->nullable();
            $table->string('AddressLine4')->nullable();
            $table->string('City')->nullable();
            $table->string('Country')->nullable();
            $table->string('State')->nullable();
            $table->string('Province')->nullable();
            $table->string('PostalCode')->nullable();
            $table->string('VendorCode')->nullable();
            $table->string('Depot')->nullable();
            $table->string('DepotRoute')->nullable();
            $table->string('EmployeeRoute')->nullable();
            $table->string('Category')->nullable();
            $table->string('Territory')->nullable();
            $table->string('status')->nullable();
            $table->string('CreateBy')->nullable();
            $table->string('UpdateBy')->nullable();
            $table->string('queue')->nullable();
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
        Schema::dropIfExists('customers');
    }
};
