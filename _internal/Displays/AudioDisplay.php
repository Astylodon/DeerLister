<?php

namespace DeerLister\Displays;

use DeerLister\ExtensionHelper;

/**
 * Provides display for images
 */
class AudioDisplay implements FileDisplay
{
    public function doesHandle(string $ext): bool
    {
        return in_array($ext, ExtensionHelper::getAudioExtensions());
    }
}