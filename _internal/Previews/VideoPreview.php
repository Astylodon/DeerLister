<?php

namespace DeerLister\Previews;

use Twig\Environment;

/**
 * Provides previews for videos
 */
class VideoPreview implements FilePreview
{
    public function doesHandle(string $filename, string $ext): bool
    {
        return in_array($ext, ["mp4", "webm"]);
    }

    public function renderPreview(string $path, string $extension, Environment $twig): string
    {
        return $twig->render("previews/video.html.twig", [ "path" => $path ]);
    }
}