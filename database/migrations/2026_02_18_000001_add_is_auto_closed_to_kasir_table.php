<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('kasir', function (Blueprint $table) {
            $table->boolean('is_auto_closed')
                  ->default(false)
                  ->after('waktu_close')
                  ->comment('true = ditutup otomatis sistem/owner, false = tutup manual');
        });
    }

    public function down(): void
    {
        Schema::table('kasir', function (Blueprint $table) {
            $table->dropColumn('is_auto_closed');
        });
    }
};
