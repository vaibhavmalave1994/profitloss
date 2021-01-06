<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
        });

        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->integer('account_id');
            $table->string('plaid_transaction_id', 255);
            $table->string('plaid_security_id', 255);
            $table->string('name', 255);
            $table->string('ticker', 255);
            $table->double('amount',8,2);
            $table->double('fees',8,2);
            $table->double('price', 8, 2);
            $table->double('quantity', 8, 2);
            $table->string('type', 255);
            $table->string('subtype', 255);
            $table->string('iso_currency_code', 255);
            $table->string('unofficial_currency_code', 255);
            $table->date('date');
            $table->rememberToken();
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
        Schema::dropIfExists('transactions');
    }
}
