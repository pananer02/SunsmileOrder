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
        Schema::create('channel_admins', function (Blueprint $table) {
            $table->id();
            $table->string('employee_code');
            $table->string('employee');
            $table->string('channel');
            $table->string('CreateBy'); 
            $table->string('UpdateBy');
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
        Schema::dropIfExists('channel_admins');
    }
};
