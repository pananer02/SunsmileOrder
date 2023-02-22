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
        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->string('ItemName');
            $table->string('ItemDescription');
            $table->string('PrimaryUOM');
            $table->string('SecondaryUOM')->nullable();
            $table->string('Category')->nullable();
            $table->string('Status');
            $table->string('image')->nullable();
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
        Schema::dropIfExists('items');
    }
};
