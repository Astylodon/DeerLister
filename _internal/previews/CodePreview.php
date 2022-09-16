<?php

require_once "FilePreview.php";

/**
 * Provides previews for code or text
 */
class CodePreview implements FilePreview
{
    const EXTENSIONS =
        [
            "txt", "js", "css", "xml", "json", "toml", "csv", "tsv",
            "yml", "yaml", "cs", "c", "h", "cpp", "hpp", "sh", "ps1",
            "py", "rs", "ts", "lua", "java", "go", "csproj", "sln",
            "kt", "sql", "dart", "rb", "asm", "vba", "fs", "pom", 
            "hs", "patch", "def", "bt", "log", "cmake", "lock", "bat",
            "vcxproj"
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

        if (in_array($filename, [".gitignore", ".gitmodules"]))
        {
            return true;
        }

        return in_array($ext, self::EXTENSIONS);
    }

    public function renderPreview(string $path, string $extension, Twig\Environment $twig): string
    {
        if (in_array($extension, [ "txt", "log", "def", "csv", "tsv" ])) $extension = "plaintext";
        else if (in_array($extension, [ "csproj", "vcxproj", "pom" ])) $extension = "xml";
        else if ($extension === "asm") $extension = "x86asm";
        else if ($extension === "lock") $extension = "json";
        else if ($extension === "bt") $extension = "c";

        $class = $extension;

        return $twig->render("previews/code.html.twig", [ "code" => file_get_contents($path), "class" => $class ]);
    }
}