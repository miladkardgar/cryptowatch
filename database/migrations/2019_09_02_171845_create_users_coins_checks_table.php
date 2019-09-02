<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersCoinsChecksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users_coins_checks', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->nullable();
            $table->integer('symbol_id')->nullable();

            $table->string('symbol')->nullable();
            $table->string('price')->nullable();
            $table->string('avg_mines')->nullable();
            $table->string('avg_price')->nullable();
            $table->string('priceChange')->nullable();
            $table->string('priceChangePercent')->nullable();
            $table->string('volume')->nullable();
            $table->string('quoteVolume')->nullable();
            $table->integer('count')->nullable();

            $table->integer('parent_id')->nullable();
            $table->float('volume_change')->nullable();
            $table->float('price_change')->nullable();
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
        Schema::dropIfExists('users_coins_checks');
    }
}
