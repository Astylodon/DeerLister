<?php

require_once "FilePreview.php";

/**
 * Provides previews for videos
 */
class VideoPreview implements FilePreview
{
    public function doesHandle(string $filename, string $ext): bool
    {
        return in_array($ext, ["mp4", "webm"]);
    }

    public function renderPreview(string $path, string $extension, Twig\Environment $twig): string
    {
        return $twig->render("previews/video.html.twig", [ "path" => $path ]);
    }
}