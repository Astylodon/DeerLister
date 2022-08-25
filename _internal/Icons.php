<?php

class Icons
{
    private static $brands = [
        'php' => 'fa-php'
    ];

    public static function getIcon(string $extension): string {
        if (array_key_exists($extension, Icons::$brands))
        {
            return 'fa-brands ' . Icons::$brands[$extension];
        }
        return 'fa-solid fa-file';
    }

    public static function getFolderIcon(): string {
        return 'fa-solid fa-folder';
    }
}