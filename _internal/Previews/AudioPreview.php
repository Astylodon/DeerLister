<?php

namespace DeerLister\Previews;

use Twig\Environment;
use DeerLister\ExtensionHelper;

/**
 * Provides previews for audios
 */
class AudioPreview implements FilePreview
{
    public function doesHandle(string $filename, string $ext): bool
    {
        return in_array($ext, ExtensionHelper::getAudioExtensions());
    }

    public function renderPreview(string $path, string $extension, Environment $twig): string
    {
        return $twig->render("previews/audio.html.twig", [ "path" => $path ]);
    }
}