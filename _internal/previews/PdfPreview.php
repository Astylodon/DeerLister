<?php

require_once "FilePreview.php";

/**
 * Provides previews for PDF
 */
class PdfPreview implements FilePreview
{
    public function doesHandle(string $filename, string $ext): bool
    {
        return in_array($ext, ["pdf"]);
    }

    public function renderPreview(string $path, string $extension, Twig\Environment $twig): string
    {
        return $twig->render("previews/pdf.html.twig", [ "path" => $path ]);
    }
}