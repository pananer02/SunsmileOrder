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
        Schema::create('order_details', function (Blueprint $table) {
            $table->id();
            $table->string("IDOrder");
            $table->string("IDCustomer")->nullable();
            $table->string("CustomerName");
            $table->string("ItemName");
            $table->string("ItemDesciption")->nullable();
            $table->string("amount");
            $table->string("UOM");
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
        Schema::dropIfExists('order_details');
    }
};
