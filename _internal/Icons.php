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
        'asm' => 'fa-file-code', // Assembly
        '7z' => 'fa-file-zipper',
        'bat' => 'fa-file-code', // Script
        'bmp' => 'fa-image',
        'c' => 'fa-file-code',
        'cpp' => 'fa-file-code',
        'cs' => 'fa-file-code', // C#
        'css' => 'fa-file-code',
        'csv' => 'fa-file-csv',
        'dart' => 'fa-file-code',
        'doc' => 'fa-file-word', // Old Word format
        'docm' => 'fa-file-word', // Word format with macro
        'docx' => 'fa-file-word', // Word format
        "flac" => 'fa-file-audio',
        'fs' => 'fa-file-code', // F#
        'gif' => 'fa-image',
        'go' => 'fa-file-code', // Golang
        'h' => 'fa-file-code', // C
        'hpp' => 'fa-file-code', // C++
        'hs' => 'fa-file-code', // Haskell
        'java' => 'fa-file-code',
        'jpeg' => 'fa-image',
        'jpg' => 'fa-image',
        'js' => 'fa-file-code', // Javascript
        'kt' => 'fa-file-code', // Kotlin
        'lua' => 'fa-file-code',
        "mp3" => 'fa-file-audio',
        "mp4" => 'fa-file-video',
        "ogg" => 'fa-file-audio',
        'pdf' => 'fa-file-pdf',
        'ppt' => 'fa-file-powerpoint', // Old Powerpoint format
        'pptm' => 'fa-file-powerpoint', // Powerpoint format with macro
        'pptx' => 'fa-file-powerpoint', // Powerpoint format
        'png' => 'fa-image',
        'ps1' => 'fa-file-code', // Powershell script
        'py' => 'fa-file-code', // Python
        'rar' => 'fa-file-zipper',
        'rb' => 'fa-file-code', // Ruby
        'rs' => 'fa-file-code', // Rust
        'sh' => 'fa-file-code', // Shell script
        'svg' => 'fa-image',
        'ts' => 'fa-file-code', // Typescript
        'ttf' => 'fa-font',
        'txt' => 'fa-file-lines',
        'vba' => 'fa-file-code', // Visual Basic for Applications
        'xls' => 'fa-file-excel', // Old Excel format
        'xlsm' => 'fa-file-excel', // Excel format with macro
        'xlsx' => 'fa-file-excel', // Excel format
        "wav" => 'fa-file-audio',
        "webm" => 'fa-file-video', // Can also be audio
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