<?php

require_once "FilePreview.php";

/**
 * Provides previews for code or text
 */
class CodePreview implements FilePreview
{
    const EXTENSIONS =
        [
            "txt", "js", "css", "xml", "json", "toml",
            "yaml", "cs", "c", "cpp", "sh", "ps1", "bat",
            "py", "rs", "ts", "lua", "java", "go", "csproj",
            "kt", "sql", "dart", "rb", "asm", "vba", "fs",
            "hs", "patch", "map", "def", "bt", "log"
        ];

    const UNSAFE_EXTENSIONS =
        [
            "php"
        ];

    public function doesHandle(string $filename, string $ext): bool
    {
        // TODO allow previews to have a config section and check for unsafe

        return in_array($ext, self::EXTENSIONS);
    }

    public function renderPreview(string $path, string $extension, Twig\Environment $twig): string
    {
        $content = file_get_contents($path);

        return $twig->render("previews/code.html.twig", [ "code" => $content ]);
    }
}