<?php

require_once "FileDisplay.php";

/**
 * Provides display for images
 */
class ImageDisplay implements FileDisplay
{
    public function doesHandle(string $ext): bool
    {
        return in_array($ext, ["mp3", "wav", "ogg", "webm", "flac"]);
    }
}