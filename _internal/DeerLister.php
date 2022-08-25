<?php

use Twig\Loader\FilesystemLoader;
use Twig\Environment;

class DeerLister
{
    private Environment $twig;

    function __construct()
    {
        // setup twig
        $loader = new FilesystemLoader("_internal/templates");

        $this->twig = new Environment($loader);
    }

    private function readDirectory(string $directory): array
    {
        $base = getcwd();
        $path = realpath($base . "/" . $directory);

        // make sure we are not accessing a folder outside the script root
        if ($path === false || strpos($path, $base))
        {
            return [];
        }

        $files = [];

        // files to exluce, could array_merge with hidden files from a config
        $exclude = ["..", "."];

        foreach(scandir($path) as $file)
        {
            if (in_array($file, $exclude))
            {
                continue;
            }

            $modified = date("Y-m-d H:i");

            array_push($files, ["name" => basename($file), "icon" => null, "lastModified" => $modified, "size" => filesize($file)]);
        }

        return $files;
    }

    public function render(string $directory): string
    {
        $files = $this->readDirectory($directory);

        return $this->twig->render("index.html.twig", ["files" => $files, "title" => "Deer Lister"]);
    }
}
