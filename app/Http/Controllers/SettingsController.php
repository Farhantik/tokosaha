<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SettingsController extends Controller
{
    public function index()
    {
        // Ambil settings dari database
        $settings = DB::table('settings')->first();
        
        // Jika belum ada, buat default
        if (!$settings) {
            DB::table('settings')->insert([
                'auto_print' => false,
                'printer_name' => '',
                'paper_width' => 58,
                'font_size' => 'medium',
                'created_at' => now(),
                'updated_at' => now()
            ]);
            
            $settings = DB::table('settings')->first();
        }
        
        return view('settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'auto_print' => 'nullable|boolean',
            'printer_name' => 'required|string',
            'paper_width' => 'nullable|integer|in:58,80',
            'font_size' => 'nullable|string|in:small,medium,large',
        ]);

        // Cek apakah settings sudah ada
        $settings = DB::table('settings')->first();
        
        $data = [
            'auto_print' => $request->boolean('auto_print'),
            'printer_name' => $request->printer_name,
            'paper_width' => $request->paper_width ?? 58,
            'font_size' => $request->font_size ?? 'medium',
            'updated_at' => now()
        ];

        if ($settings) {
            // Update existing settings
            DB::table('settings')->where('id', $settings->id)->update($data);
        } else {
            // Insert new settings
            $data['created_at'] = now();
            DB::table('settings')->insert($data);
        }

        return redirect()->back()->with('success', 'Pengaturan berhasil disimpan! Auto print akan aktif di menu transaksi.');
    }

    // Method untuk API (jika diperlukan)
    public function getSettings()
    {
        $settings = DB::table('settings')->first();
        
        if (!$settings) {
            $settings = (object) [
                'auto_print' => false,
                'printer_name' => '',
                'paper_width' => 58,
                'font_size' => 'medium'
            ];
        }
        
        return response()->json([
            'success' => true,
            'data' => $settings
        ]);
    }
}