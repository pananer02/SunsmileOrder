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
        Schema::create('depot_routes', function (Blueprint $table) {
            $table->id();
            $table->string('RoutesName');
            $table->string('Status');
            $table->string('CreateBy');
            $table->string('UpdateBy');
            $table->string('DepotID')->nullable();
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
        Schema::dropIfExists('depot_routes');
    }
};
