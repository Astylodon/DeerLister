<?php

require_once "FilePreview.php";

/**
 * Provides previews for code or text
 */
class CodePreview implements FilePreview
{
    public function doesHandle(string $filename): bool
    {
        $ext = pathinfo($filename, PATHINFO_EXTENSION);

        // couple examples
        return in_array($ext, ["txt", "js", "css", "cs", "c", "cpp", "py", "sh", "bat", "ps1", "go"]);
    }

    public function renderPreview(string $path, Twig\Environment $twig): string
    {
        return $twig->render("previews/code.html.twig", [ "code" => file_get_contents($path) ]);
    }
}