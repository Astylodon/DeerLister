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

    private array $config;

    function __construct(array $config)
    {
        $this->config = $config;
    }

    public function doesHandle(string $filename, string $ext): bool
    {
        if (isset($this->config["previews"]["code"]["unsafe"]) && $this->config["previews"]["code"]["unsafe"])
        {
            if (in_array($ext, self::UNSAFE_EXTENSIONS))
            {
                return true;
            }
        }

        return in_array($ext, self::EXTENSIONS);
    }

    public function renderPreview(string $path, string $extension, Twig\Environment $twig): string
    {
        $content = file_get_contents($path);

        return $twig->render("previews/code.html.twig", [ "code" => $content ]);
    }
}