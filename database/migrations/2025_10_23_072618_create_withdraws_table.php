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
        Schema::create('withdraws', function (Blueprint $table) {
            $table->id();
            $table->foreignId('store_id')->constrained()->onDelete('cascade')->onUpdate('cascade');
            $table->string('name'); // bisa jadi nama pemilik rekening
            $table->string('status')->default('pending'); // pending, approved, rejected
            $table->string('payment_type'); // e.g. bank, e-wallet
            $table->integer('amount');
            $table->string('no_rek')->nullable();
            $table->timestamp('settled_at')->nullable();
            $table->string('note')->nullable(); // untuk admin
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('withdraws');
    }
};
