<?php

namespace DeerLister\Previews;

use Twig\Environment;

/**
 * Provides previews for audios
 */
class AudioPreview implements FilePreview
{
    public function doesHandle(string $filename, string $ext): bool
    {
        return in_array($ext, ["mp3", "wav", "ogg", "webm", "flac"]);
    }

    public function renderPreview(string $path, string $extension, Environment $twig): string
    {
        return $twig->render("previews/audio.html.twig", [ "path" => $path ]);
    }
}