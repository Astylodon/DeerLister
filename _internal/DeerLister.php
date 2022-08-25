<?php

require_once 'Icons.php';

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

    private function readDirectory(string $directory): array|false
    {
        $base = getcwd();
        $path = realpath($base . "/" . $directory);

        // make sure we are not accessing a folder outside the script root
        if ($path === false || strpos($path, $base) !== 0)
        {
            return false;
        }

        if (!is_dir($path))
        {
            return false;
        }

        $files = [];

        // files to exclude, could array_merge with hidden files from a config
        $exclude = ["..", ".", "_internal", "vendor"];

        foreach(scandir($path) as $name)
        {
            if (in_array($name, $exclude))
            {
                continue;
            }

            $file = realpath($path . "/" . $name);
            $modified = date("Y-m-d H:i", filemtime($file));

            $is_folder = is_dir($file);

            array_push($files, ["name" => $name, "icon" => $is_folder ? Icons::getFolderIcon() : Icons::getIcon(pathinfo($file, PATHINFO_EXTENSION)), "lastModified" => $modified, "size" => filesize($file)]);
        }

        return $files;
    }

    public function render(string $directory): string
    {
        if (($files = $this->readDirectory($directory)) === false)
        {
            return $this->twig->render("404.html.twig", ["title" => "Deer Lister"]);
        }

        return $this->twig->render("index.html.twig", ["files" => $files, "title" => "Deer Lister"]);
    }
}
