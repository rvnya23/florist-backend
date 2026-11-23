<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rekening_bank', function (Blueprint $table) {
            $table->id();
            $table->string('nama_bank');
            $table->string('nomor_rekening')->unique();
            $table->string('nama_pemilik');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('carts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_id')->unique()->constrained()->cascadeOnDelete();
            $table->timestamps();
        });

        Schema::create('cart_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cart_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('quantity');
            $table->timestamps();
            $table->unique(['cart_id', 'product_id']);
        });

        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_id')->constrained()->restrictOnDelete();
            $table->string('order_code')->unique();
            $table->timestamp('order_date');
            $table->unsignedBigInteger('total_harga');
            $table->text('alamat_pengiriman');
            $table->text('catatan')->nullable();
            $table->string('status')->default('pending_payment')->index();
            $table->timestamps();
        });

        Schema::create('order_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->restrictOnDelete();
            $table->unsignedBigInteger('harga_satuan');
            $table->unsignedInteger('jumlah');
            $table->unsignedBigInteger('subtotal');
            $table->timestamps();
        });

        Schema::create('transaksi_order', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->unique()->constrained()->cascadeOnDelete();
            $table->foreignId('rekening_bank_id')->constrained('rekening_bank')->restrictOnDelete();
            $table->string('kode_pembayaran')->unique();
            $table->string('nama_pengirim');
            $table->date('tanggal_pembayaran');
            $table->unsignedBigInteger('jumlah_pembayaran');
            $table->string('bukti_pembayaran');
            $table->string('status')->default('pending')->index();
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('verified_at')->nullable();
            $table->text('catatan_penolakan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transaksi_order');
        Schema::dropIfExists('order_details');
        Schema::dropIfExists('orders');
        Schema::dropIfExists('cart_items');
        Schema::dropIfExists('carts');
        Schema::dropIfExists('rekening_bank');
    }
};
