<?php

namespace DeerLister\Displays;

/**
 * Provides display for images
 */
class ImageDisplay implements FileDisplay
{
    public function doesHandle(string $ext): bool
    {
        return in_array($ext, ["apng", "png", "jpg", "jpeg", "gif", "svg", "webp"]);
    }
}