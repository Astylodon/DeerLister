<?php

namespace DeerLister;

class Icons
{
    private static $brands =
    [
        'md'    => 'fa-markdown',
        'apk'   => 'fa-android',
        'jar'   => 'fa-java'
    ];

    private static $solids =
    [
        'txt' => 'fa-file-lines',
        'log' => 'fa-file-lines',
        'ttf' => 'fa-font',

        '7z'    => 'fa-file-zipper',
        'rar'   => 'fa-file-zipper',
        'zip'   => 'fa-file-zipper',
        'tar'   => 'fa-file-zipper',
        'iso'   => 'fa-file-zipper',
        'gz'    => 'fa-file-zipper',

        'csv'   => 'fa-file-csv',
        'tsv'   => 'fa-file-csv',
        'doc'   => 'fa-file-word', // Old Word format
        'docm'  => 'fa-file-word', // Word format with macro
        'docx'  => 'fa-file-word', // Word format
        'pdf'   => 'fa-file-pdf',
        'ppt'   => 'fa-file-powerpoint', // Old Powerpoint format
        'pptm'  => 'fa-file-powerpoint', // Powerpoint format with macro
        'pptx'  => 'fa-file-powerpoint', // Powerpoint format
        'xls'   => 'fa-file-excel', // Old Excel format
        'xlsm'  => 'fa-file-excel', // Excel format with macro
        'xlsx'  => 'fa-file-excel', // Excel format

        'c'     => 'fa-file-code',
        'cpp'   => 'fa-file-code',
        'cs'    => 'fa-file-code', // C#
        'css'   => 'fa-file-code',
        'asm'   => 'fa-file-code', // Assembly
        'bat'   => 'fa-file-code', // Batch
        'php'   => 'fa-file-code',
        'fs'    => 'fa-file-code', // F#
        'go'    => 'fa-file-code', // Golang
        'h'     => 'fa-file-code', // C
        'hpp'   => 'fa-file-code', // C++
        'hs'    => 'fa-file-code', // Haskell
        'java'  => 'fa-file-code',
        'dart'  => 'fa-file-code',
        'js'    => 'fa-file-code', // Javascript
        'kt'    => 'fa-file-code', // Kotlin
        'lua'   => 'fa-file-code',
        'ps1'   => 'fa-file-code', // Powershell script
        'py'    => 'fa-file-code', // Python
        'rb'    => 'fa-file-code', // Ruby
        'rs'    => 'fa-file-code', // Rust
        'sh'    => 'fa-file-code', // Shell script
        'ts'    => 'fa-file-code', // Typescript
        'vba'   => 'fa-file-code', // Visual Basic for Applications
        'xml'   => 'fa-file-code',
        'html'  => 'fa-file-code',
        'json'  => 'fa-file-code',
        'toml'  => 'fa-file-code',
        'yml'   => 'fa-file-code',
        'yaml'  => 'fa-file-code',
        'sql'   => 'fa-file-code',
        'pom'   => 'fa-file-code',
        'patch' => 'fa-file-code', // Git patch
        'def'   => 'fa-file-code',
        'bt'    => 'fa-file-code', // 010 Editor Binary Template
        'cmake' => 'fa-file-code',

        'csproj'    => 'fa-file-code', // C# Project
        'vcxproj'   => 'fa-file-code', // Visual Studio C/C++ Project
        'sln'       => 'fa-file-code', // Visual Studio solution

        'exe'   => 'fa-gears',
        'dll'   => 'fa-gears',
        'so'    => 'fa-gears',
        'a'     => 'fa-gears',
        'wasm'  => 'fa-gears',

        'db'        => 'fa-database',
        'sqlite'    => 'fa-database',

        'png'   => 'fa-image',
        'gif'   => 'fa-image',
        'jpeg'  => 'fa-image',
        'jpg'   => 'fa-image',
        'svg'   => 'fa-image',
        'apng'  => 'fa-image', // Animated PNG
        'bmp'   => 'fa-image',
        'webp'  => 'fa-image',
        'tga'   => 'fa-image',
        'dds'   => 'fa-image',
        
        "wav"   => 'fa-file-audio',
        "webm"  => 'fa-file-video', // Can also be audio
        "mp3"   => 'fa-file-audio',
        "mp4"   => 'fa-file-video',
        "ogg"   => 'fa-file-audio',
        'flac'  => 'fa-file-audio',
        'mkv'   => 'fa-file-video',
        'avi'   => 'fa-file-video',
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