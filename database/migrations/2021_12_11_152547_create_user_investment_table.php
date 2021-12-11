<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserInvestmentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_investment', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('investment_product_id');
            $table->unsignedDecimal('balance', 13, 2);
            $table->unsignedDecimal('unit', 13, 4);
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('investment_product_id')->references('id')->on('investment_products');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_investment');
    }
}
