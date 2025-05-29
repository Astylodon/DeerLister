<?php

namespace DeerLister\Previews;

use Twig\Environment;

/**
 * Provides previews for images
 */
class ImagePreview implements FilePreview
{
    public function doesHandle(string $filename, string $ext): bool
    {
        return in_array($ext, ["apng", "png", "jpg", "jpeg", "gif", "svg", "webp"]);
    }

    public function renderPreview(string $path, string $extension, Environment $twig): string
    {
        return $twig->render("previews/image.html.twig", [ "path" => $path ]);
    }
}