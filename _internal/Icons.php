<?php

class Icons
{
    private static $brands =
    [
        'md' => 'fa-markdown',
        'php' => 'fa-php'
    ];

    private static $solids =
    [
        'apng' => 'fa-image', // Animated PNG
        'asm' => 'fa-code', // Assembly
        '7z' => 'fa-file-zipper',
        'bat' => 'fa-code', // Script
        'bmp' => 'fa-image',
        'c' => 'fa-code',
        'cpp' => 'fa-code',
        'cs' => 'fa-code', // C#
        'css' => 'fa-code',
        'csv' => 'fa-file-csv',
        'dart' => 'fa-code',
        'doc' => 'fa-file-word', // Old Word format
        'docm' => 'fa-file-word', // Word format with macro
        'docx' => 'fa-file-word', // Word format
        'fs' => 'fa-code', // F#
        'gif' => 'fa-image',
        'go' => 'fa-code', // Golang
        'h' => 'fa-code', // C
        'hpp' => 'fa-code', // C++
        'hs' => 'fa-code', // Haskell
        'java' => 'fa-code',
        'jpeg' => 'fa-image',
        'jpg' => 'fa-image',
        'js' => 'fa-code', // Javascript
        'kt' => 'fa-code', // Kotlin
        'lua' => 'fa-code',
        'pdf' => 'fa-file-pdf',
        'ppt' => 'fa-file-powerpoint', // Old Powerpoint format
        'pptm' => 'fa-file-powerpoint', // Powerpoint format with macro
        'pptx' => 'fa-file-powerpoint', // Powerpoint format
        'png' => 'fa-image',
        'ps1' => 'fa-code', // Powershell script
        'py' => 'fa-code', // Python
        'rar' => 'fa-file-zipper',
        'rb' => 'fa-code', // Ruby
        'rs' => 'fa-code', // Rust
        'sh' => 'fa-code', // Shell script
        'svg' => 'fa-image',
        'ts' => 'fa-code', // Typescript
        'ttf' => 'fa-font',
        'txt' => 'fa-file-lines',
        'vba' => 'fa-code', // Visual Basic for Applications
        'xls' => 'fa-file-excel', // Old Excel format
        'xlsm' => 'fa-file-excel', // Excel format with macro
        'xlsx' => 'fa-file-excel', // Excel format
        'webp' => 'fa-image',
        'zip' => 'fa-file-zipper'
    ];

    public static function getIcon(string $extension): string
    {
        if (array_key_exists($extension, Icons::$brands))
        {
            return 'fa-brands ' . Icons::$brands[$extension];
        }
        if (array_key_exists($extension, Icons::$solids))
        {
            return 'fa-solid ' . Icons::$solids[$extension];
        }
        return 'fa-solid fa-file';
    }

    public static function getFolderIcon(): string
    {
        return 'fa-solid fa-folder';
    }
}