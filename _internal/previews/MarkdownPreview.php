<?php

require_once "FilePreview.php";

/**
 * Provides previews for markdown
 */
class MarkdownPreview implements FilePreview
{
    public function doesHandle(string $filename, string $ext): bool
    {
        return in_array($ext, ["md"]);
    }

    public function renderPreview(string $path, string $extension, Twig\Environment $twig): string
    {
        $parsedown = new ParsedownExtension();
        $parsedown->setSafeMode(true);
        $content = $parsedown->text(file_get_contents($path));

        return $twig->render("previews/markdown.html.twig", [ "code" => $content ]);
    }
}