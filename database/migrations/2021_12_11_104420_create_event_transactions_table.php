<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEventTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('event_transaction_histories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('investment_product_id');
            $table->enum('type', ['debit', 'credit']);
            $table->unsignedDecimal('nab', 13, 4)->default(0);
            $table->unsignedDecimal('unit', 13, 4)->default(0);
            $table->unsignedDecimal('total_unit', 13, 4)->default(0);
            $table->unsignedDecimal('amount', 13, 2)->default(0);
            $table->unsignedDecimal('balance', 13, 2)->default(0);
            $table->dateTime('datetime');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('event_transaction_histories');
    }
}
