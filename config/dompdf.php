<?php

return [
    'show_warnings'   => false,
    'orientation'     => 'portrait',

    'options' => [
        'font_dir'                => storage_path('fonts'),
        'font_cache'              => storage_path('fonts'),
        'temp_dir'                => sys_get_temp_dir(),
        'chroot'                  => realpath(base_path()),
        'allowed_protocols'       => ['file://', 'http://', 'https://'],
        'artifactPathValidation'  => true,
        'log_output_file'         => null,
        'font_height_ratio'       => 1.1,

        'isPhpEnabled'            => false,
        'isRemoteEnabled'         => false,
        'isJavascriptEnabled'     => false,
        'isFontSubsettingEnabled' => true,

        // ✅ PERBAIKAN UTAMA: ganti 'screen' → 'print'
        // Mode 'screen' menyebabkan @page tidak diterapkan dengan benar
        // sehingga lebar halaman salah dan konten terpotong
        'defaultMediaType'        => 'print',

        'defaultPaperSize'        => 'a4',
        'defaultPaperOrientation' => 'portrait',
        'defaultFont'             => 'dejavu sans',

        // ✅ Naikkan DPI agar teks & layout lebih presisi
        'dpi'                     => 96,

        'enable_php'              => false,
        'enable_javascript'       => false,
        'enable_remote'           => false,
    ],
];
