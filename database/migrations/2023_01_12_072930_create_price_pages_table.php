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
        Schema::create('price_pages', function (Blueprint $table) {
            $table->id();
            $table->string('channel');
            $table->string('depot');
            $table->string('PriceID');
            $table->string('name_file');
            $table->string('file')->nullable();
            $table->string('image')->nullable();
            $table->string('Status');
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
        Schema::dropIfExists('price_pages');
    }
};
