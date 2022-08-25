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
        if ($path === false || strpos($path, $base) !== 0)
        {
            return [];
        }

        $files = [];

        // files to exluce, could array_merge with hidden files from a config
        $exclude = ["..", ".", "_internal", "vendor"];

        foreach(scandir($path) as $name)
        {
            if (in_array($name, $exclude))
            {
                continue;
            }

            $file = realpath($path . "/" . $name);
            $modified = date("Y-m-d H:i", filemtime($file));

            array_push($files, ["name" => $name, "icon" => null, "lastModified" => $modified, "size" => filesize($file)]);
        }

        return $files;
    }

    public function render(string $directory): string
    {
        $files = $this->readDirectory($directory);

        return $this->twig->render("index.html.twig", ["files" => $files, "title" => "Deer Lister"]);
    }
}
