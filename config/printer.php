<?php

// config/printer.php
return [
    /*
    |--------------------------------------------------------------------------
    | Printer Configuration
    |--------------------------------------------------------------------------
    |
    | Konfigurasi printer thermal untuk sistem POS
    |
    */

    'default' => [
        'type' => env('PRINTER_TYPE', 'windows'),
        'name' => env('PRINTER_NAME', 'POS-58'),
        'ip' => env('PRINTER_IP', null),
        'port' => env('PRINTER_PORT', 9100),
        'path' => env('PRINTER_PATH', null),
        'interface' => env('PRINTER_INTERFACE', 'network'), // network, usb, serial
    ],

    /*
    |--------------------------------------------------------------------------
    | Printer Types
    |--------------------------------------------------------------------------
    |
    | windows: Windows shared printer
    | network: IP printer (ESC/POS compatible)
    | usb: Direct USB connection
    | serial: Serial/COM port
    |
    */

    'types' => [
        'windows' => [
            'driver' => 'windows_share',
            'description' => 'Windows Shared Printer'
        ],
        'network' => [
            'driver' => 'network_socket',
            'description' => 'Network IP Printer'
        ],
        'usb' => [
            'driver' => 'usb_direct',
            'description' => 'USB Direct Connection'
        ],
        'serial' => [
            'driver' => 'serial_port',
            'description' => 'Serial/COM Port'
        ]
    ],

    /*
    |--------------------------------------------------------------------------
    | Paper Settings
    |--------------------------------------------------------------------------
    */

    'paper' => [
        'width' => env('PRINTER_PAPER_WIDTH', 58), // 58mm or 80mm
        'charset' => env('PRINTER_CHARSET', 'cp437'),
        'line_length' => env('PRINTER_LINE_LENGTH', 32), // characters per line
    ],

    /*
    |--------------------------------------------------------------------------
    | Receipt Settings
    |--------------------------------------------------------------------------
    */

    'receipt' => [
        'auto_cut' => env('PRINTER_AUTO_CUT', true),
        'double_height' => env('PRINTER_DOUBLE_HEIGHT', true),
        'logo_enabled' => env('PRINTER_LOGO_ENABLED', false),
        'footer_lines' => env('PRINTER_FOOTER_LINES', 3),
    ]
];