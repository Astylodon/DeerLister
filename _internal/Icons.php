<?php

class Icons
{
    private static $brands =
    [
        'md' => 'fa-markdown',
        'php' => 'fa-php',
        'ttf' => 'fa-font'
    ];

    private static $solids =
    [
        '7z' => 'fa-file-zipper',
        'bmp' => 'fa-image',
        'csv' => 'fa-file-csv',
        'doc' => 'fa-file-word', // Old Word format
        'docm' => 'fa-file-word', // Word format with macro
        'docx' => 'fa-file-word', // Word format
        'gif' => 'fa-image',
        'jpeg' => 'fa-image',
        'jpg' => 'fa-image',
        'pdf' => 'fa-file-pdf',
        'ppt' => 'fa-file-powerpoint', // Old Powerpoint format
        'pptm' => 'fa-file-powerpoint', // Powerpoint format with macro
        'pptx' => 'fa-file-powerpoint', // Powerpoint format
        'png' => 'fa-image',
        'rar' => 'fa-file-zipper',
        'txt' => 'fa-file-lines',
        'xls' => 'fa-file-excel', // Old Excel format
        'xlsm' => 'fa-file-excel', // Excel format with macro
        'xlsx' => 'fa-file-excel', // Excel format
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
            return 'fa-solid ' . Icons::$brands[$extension];
        }
        return 'fa-solid fa-file';
    }

    public static function getFolderIcon(): string
    {
        return 'fa-solid fa-folder';
    }
}