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
        Schema::create('click_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('click_trans_id');
            $table->integer('service_id');
            $table->string('click_paydoc_id');
            $table->string('merchant_trans_id', 255);
            $table->float('amount', 8, 2);
            $table->integer('action');
            $table->integer('error');
            $table->tinyInteger('status')->nullable();
            $table->string('error_note', 255);
            $table->string('sign_time', 255);
            $table->string('sign_string', 255);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('click_transactions');
    }
};
