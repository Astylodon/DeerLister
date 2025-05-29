<?php

namespace DeerLister\Previews;

use Twig\Environment;

interface FilePreview
{
    /**
     * Whether the current file preview handles this file
     * 
     * @param string $filename The name of the file
     * @param string $ext The file extension without leading dot
     * 
     * @return bool Whether the file preview handles the file
     */
    public function doesHandle(string $filename, string $ext): bool;

    /**
     * Render the file preview content
     * 
     * @param string $path The relative path to the file
     * @param string $extension The file extension without leading dot
     * @param Environment $twig Instance of twig which can be used to render the file preview
     * 
     * @return string The file preview content
     */
    public function renderPreview(string $path, string $extension, Environment $twig): string;
}