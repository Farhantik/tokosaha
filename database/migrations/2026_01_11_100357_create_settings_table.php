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
        Schema::createIfNotExists('settings', function (Blueprint $table) {
            $table->id();
            $table->boolean('auto_print')->default(false);
            $table->string('printer_name')->nullable();
            $table->integer('paper_width')->default(58);
            $table->string('font_size')->default('medium');
            $table->timestamps();
        });
        
        // Insert default settings
        DB::table('settings')->insert([
            'auto_print' => false,
            'printer_name' => '',
            'paper_width' => 58,
            'font_size' => 'medium',
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};