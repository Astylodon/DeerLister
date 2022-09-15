<?php

require_once "FileDisplay.php";

/**
 * Provides display for images
 */
class ImageDisplay implements FileDisplay
{
    public function doesHandle(string $filename, string $ext): bool
    {
        return in_array($ext, ["apng", "png", "jpg", "jpeg", "gif", "svg", "webp"]);
    }
}