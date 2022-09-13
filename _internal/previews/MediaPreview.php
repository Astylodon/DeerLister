<?php

require_once "FilePreview.php";

/**
 * Provides previews for media such as images and videos
 */
class MediaPreview implements FilePreview
{
    public function doesHandle(string $filename, string $ext): bool
    {
        return in_array($ext, ["png", "jpg", "jpeg", "gif", "mp4"]);
    }

    public function renderPreview(string $path, string $extension, Twig\Environment $twig): string
    {
        $video = pathinfo($path, PATHINFO_EXTENSION) == "mp4";

        return $twig->render("previews/media.html.twig", [ "path" => $path, "isVideo" => $video ]);
    }
}