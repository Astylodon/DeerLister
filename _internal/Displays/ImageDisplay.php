<?php

namespace DeerLister\Displays;

use DeerLister\ExtensionHelper;

/**
 * Provides display for images
 */
class ImageDisplay implements FileDisplay
{
    public function doesHandle(string $ext): bool
    {
        return in_array($ext, ExtensionHelper::getImageExtensions());
    }
}