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
        // Tambah soft deletes ke tabel pelanggan
        Schema::table('pelanggan', function (Blueprint $table) {
            $table->softDeletes();
        });

        // Tambah soft deletes ke tabel piutang
        Schema::table('piutang', function (Blueprint $table) {
            $table->softDeletes();
        });

        // Tambah soft deletes ke tabel penjualan
        Schema::table('penjualan', function (Blueprint $table) {
            $table->softDeletes();
        });

        // Tambah soft deletes ke tabel penjualan_detail
        Schema::table('penjualan_detail', function (Blueprint $table) {
            $table->softDeletes();
        });

        // Tambah soft deletes ke tabel produk
        Schema::table('produk', function (Blueprint $table) {
            $table->softDeletes();
        });

        // Tambah soft deletes ke tabel supplier
        if (Schema::hasTable('supplier')) {
            Schema::table('supplier', function (Blueprint $table) {
                $table->softDeletes();
            });
        }

        // Tambah soft deletes ke tabel penerimaan
        if (Schema::hasTable('penerimaan')) {
            Schema::table('penerimaan', function (Blueprint $table) {
                $table->softDeletes();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pelanggan', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('piutang', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('penjualan', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('penjualan_detail', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('produk', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        if (Schema::hasTable('supplier')) {
            Schema::table('supplier', function (Blueprint $table) {
                $table->dropSoftDeletes();
            });
        }

        if (Schema::hasTable('penerimaan')) {
            Schema::table('penerimaan', function (Blueprint $table) {
                $table->dropSoftDeletes();
            });
        }
    }
};
