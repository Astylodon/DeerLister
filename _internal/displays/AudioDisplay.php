<?php

require_once "FileDisplay.php";

/**
 * Provides display for images
 */
class AudioDisplay implements FileDisplay
{
    public function doesHandle(string $ext): bool
    {
        return in_array($ext, ["mp3", "wav", "ogg", "webm", "flac"]);
    }
}