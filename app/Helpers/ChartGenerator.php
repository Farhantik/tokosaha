<?php

namespace App\Helpers;

class ChartGenerator
{
    /**
     * Generate chart penjualan harian sebagai base64 PNG
     *
     * @param array $dataHarian  format: [['tanggal' => '2026-01-01', 'total' => 500000, 'jumlah' => 5], ...]
     * @return string|null       base64 string atau null jika gagal
     */
    public static function penjualan(array $dataHarian): ?string
    {
        if (empty($dataHarian)) return null;
        if (!function_exists('imagecreatetruecolor')) return null;

        // ── Ukuran canvas ─────────────────────────────────────
        $canvasW  = 900;
        $canvasH  = 280;
        $padLeft  = 70;
        $padRight = 20;
        $padTop   = 30;
        $padBot   = 50;

        $chartW = $canvasW - $padLeft - $padRight;
        $chartH = $canvasH - $padTop  - $padBot;

        // ── Buat canvas ───────────────────────────────────────
        $img = imagecreatetruecolor($canvasW, $canvasH);
        imagealphablending($img, true);

        // ── Warna ─────────────────────────────────────────────
        $cBg        = imagecolorallocate($img, 248, 250, 255);
        $cGrid      = imagecolorallocate($img, 226, 232, 240);
        $cAxis      = imagecolorallocate($img, 100, 116, 139);
        $cBar       = imagecolorallocate($img,   5, 150, 105);
        $cBarLight  = imagecolorallocate($img, 167, 243, 208);
        $cLine      = imagecolorallocate($img,  37,  99, 235);
        $cLineDot   = imagecolorallocate($img,  59, 130, 246);
        $cText      = imagecolorallocate($img,  30,  41,  59);
        $cTextLight = imagecolorallocate($img, 107, 114, 128);
        $cWhite     = imagecolorallocate($img, 255, 255, 255);

        // ── Background ────────────────────────────────────────
        imagefilledrectangle($img, 0, 0, $canvasW, $canvasH, $cBg);

        // ── Skala max ─────────────────────────────────────────
        $maxOmzet = max(array_column($dataHarian, 'total') ?: [1]);
        $maxTrx   = max(array_column($dataHarian, 'jumlah') ?: [1]);
        $maxOmzet = ceil($maxOmzet / 100000) * 100000 ?: 100000;
        $maxTrx   = ceil($maxTrx / 5) * 5 ?: 5;

        $count = count($dataHarian);
        $barW  = max(8, (int)($chartW / $count) - 4);
        $stepX = $chartW / $count;

        // ── Grid horizontal ───────────────────────────────────
        for ($i = 0; $i <= 5; $i++) {
            $y = $padTop + $chartH - (int)(($i / 5) * $chartH);
            imagesetthickness($img, 1);
            imageline($img, $padLeft, $y, $padLeft + $chartW, $y, $cGrid);

            // Label Y kiri (omzet)
            $omzetVal = ($i / 5) * $maxOmzet;
            $omzetStr = $omzetVal >= 1000000
                ? number_format($omzetVal / 1000000, 1) . 'jt'
                : number_format($omzetVal / 1000, 0) . 'rb';
            imagestring($img, 1, 2, $y - 5, $omzetStr, $cTextLight);

            // Label Y kanan (transaksi)
            $trxStr = (int)(($i / 5) * $maxTrx) . ' trx';
            imagestring($img, 1, $padLeft + $chartW + 3, $y - 5, $trxStr, $cLineDot);
        }

        // ── Sumbu X & Y ───────────────────────────────────────
        imagesetthickness($img, 2);
        imageline($img, $padLeft, $padTop, $padLeft, $padTop + $chartH, $cAxis);
        imageline($img, $padLeft, $padTop + $chartH, $padLeft + $chartW, $padTop + $chartH, $cAxis);
        imagesetthickness($img, 1);

        // ── Bar + titik garis ─────────────────────────────────
        $linePoints = [];
        $showEvery  = max(1, (int)ceil($count / 15));

        foreach ($dataHarian as $idx => $row) {
            $omzet  = (float)($row['total']   ?? 0);
            $jumlah = (int)  ($row['jumlah']  ?? 0);
            $tgl    = $row['tanggal'] ?? '';

            $cx = $padLeft + (int)(($idx + 0.5) * $stepX);

            // Bar omzet
            $barH  = ($maxOmzet > 0) ? (int)(($omzet / $maxOmzet) * $chartH) : 0;
            $barX1 = $cx - (int)($barW / 2);
            $barX2 = $cx + (int)($barW / 2);
            $barY1 = $padTop + $chartH - $barH;
            $barY2 = $padTop + $chartH - 1;

            if ($barH > 0) {
                imagefilledrectangle($img, $barX1, $barY1, $barX2,     $barY2, $cBarLight);
                imagefilledrectangle($img, $barX1, $barY1, $barX2 - 2, $barY2, $cBar);
            }

            // Titik garis transaksi
            $lineY          = $padTop + $chartH - (int)(($jumlah / max($maxTrx, 1)) * $chartH);
            $linePoints[]   = ['x' => $cx, 'y' => $lineY];

            // Label tanggal
            if ($idx % $showEvery === 0) {
                $tglShort = date('d/m', strtotime($tgl));
                imagestring($img, 1, $cx - 10, $padTop + $chartH + 6, $tglShort, $cTextLight);
            }
        }

        // ── Garis transaksi ───────────────────────────────────
        if (count($linePoints) > 1) {
            imagesetthickness($img, 2);
            for ($i = 0; $i < count($linePoints) - 1; $i++) {
                imageline(
                    $img,
                    $linePoints[$i]['x'],
                    $linePoints[$i]['y'],
                    $linePoints[$i + 1]['x'],
                    $linePoints[$i + 1]['y'],
                    $cLine
                );
            }
            imagesetthickness($img, 1);

            foreach ($linePoints as $pt) {
                imagefilledellipse($img, $pt['x'], $pt['y'], 6, 6, $cWhite);
                imagefilledellipse($img, $pt['x'], $pt['y'], 4, 4, $cLineDot);
            }
        }

        // ── Legend ────────────────────────────────────────────
        $legY = $canvasH - 18;
        imagefilledrectangle($img, $padLeft, $legY, $padLeft + 12, $legY + 10, $cBar);
        imagestring($img, 1, $padLeft + 15, $legY, 'Omzet', $cText);
        imageline($img, $padLeft + 70, $legY + 5, $padLeft + 82, $legY + 5, $cLine);
        imagefilledellipse($img, $padLeft + 76, $legY + 5, 5, 5, $cLineDot);
        imagestring($img, 1, $padLeft + 85, $legY, 'Transaksi', $cText);

        // ── Output base64 ─────────────────────────────────────
        ob_start();
        imagepng($img);
        $raw = ob_get_clean();
        imagedestroy($img);

        return base64_encode($raw);
    }
}
