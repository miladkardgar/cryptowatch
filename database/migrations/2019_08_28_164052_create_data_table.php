<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDataTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('data', function (Blueprint $table) {
            $table->increments('id');
            $table->text('data')->nullable();
            $table->string('symbol')->nullable();
            $table->string('price')->nullable();
            $table->string('avg_mines')->nullable();
            $table->string('avg_price')->nullable();
            $table->string('priceChange')->nullable();
            $table->string('priceChangePercent')->nullable();
            $table->string('volume')->nullable();
            $table->string('quoteVolume')->nullable();
            $table->string('count')->nullable();
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
        Schema::dropIfExists('data');
    }
}
