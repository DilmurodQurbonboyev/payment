<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('payme_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('paycom_transaction_id')->nullable();
            $table->string('paycom_time')->nullable();
            $table->dateTime('paycom_time_datetime')->nullable();
            $table->dateTime('create_time')->nullable();
            $table->dateTime('perform_time')->nullable();
            $table->dateTime('cancel_time')->nullable();
            $table->float('amount');
            $table->tinyInteger('state');
            $table->tinyInteger('reason')->nullable();
            $table->text('receivers')->nullable();
//            $table->foreignId('charity_id')->nullable()->references('id')->on('charities')->onDelete('cascade');
            $table->integer('product_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payme_transactions');
    }
};
