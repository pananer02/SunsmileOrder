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
        Schema::create('distrbutions', function (Blueprint $table) {
            $table->id();
            $table->string("Depot"); //ดีโป้
            $table->string("ShipDate"); //วันที่ส่ง
            $table->string("Day_ShipDate"); //วันที่ส่งที่มีวันนำหน้า
            $table->string("Queue")->nullable();
            $table->string("Route")->nullable(); //สายรถ
            
            $table->string("CustomerNo"); //ID ลูกค้า
            $table->string("CustomerName"); //ชื่อลูกค้า
            $table->string("ItemNumber"); //ID สินค้า
            $table->string("ItemDescription");// ชื่อสินค้า
            $table->string("Qty"); //จำนวน
            $table->string("Uom");
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
        Schema::dropIfExists('distrbutions');
    }
};
