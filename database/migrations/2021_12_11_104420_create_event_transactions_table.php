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
        Schema::create('event_transactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('investment_product_id');
            $table->enum('type', ['debit', 'credit']);
            $table->unsignedDouble('nab')->default(0);
            $table->unsignedDouble('unit')->default(0);
            $table->unsignedDouble('total_unit')->default(0);
            $table->unsignedBigInteger('amount')->default(0);
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
        Schema::dropIfExists('event_transactions');
    }
}
