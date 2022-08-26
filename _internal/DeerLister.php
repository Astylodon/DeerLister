<?php

require_once 'Icons.php';
require_once 'ParsedownExtension.php';

use Twig\Loader\FilesystemLoader;
use Twig\Environment;
use Twig\TwigFilter;

class DeerLister
{
    private Environment $twig;

    function __construct()
    {
        // setup twig
        $loader = new FilesystemLoader("_internal/templates");

        $this->twig = new Environment($loader);

        // Convert a size in byte to something more diggest
        $this->twig->addFilter(new TwigFilter("humanFileSize", function($size) {
            $units = ["B", "KB", "MB", "GB"];
            for ($i = 0; $size > 1024; $i++) $size /= 1024;

            return round($size, 2) . $units[$i];
        }));

        // Get the file where the user with be led from the current element
        $this->twig->addFilter(new TwigFilter("getFilePath", function($file, $directory) {
            if ($file["name"] === "..")
            {
                return "?dir=" . dirname(dirname($directory . $file["name"]));
            }
            $path = "";
            if ($file["isFolder"])
            {
                $path .= "?dir=";
            }
            return $path . $directory . $file["name"];
        }));

        // Build the path until the current index
        $this->twig->addFilter(new TwigFilter("buildPath", function($pathArray, $index) {
            $finalPath = "";
            foreach ($pathArray as $index => $value)
            {
                if ($value === $index) {
                    break;
                }
                $finalPath .= $value . '/';
            }
        }));
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

        if ($this->isHidden($directory))
        {
            return false;
        }

        $files = [];

        foreach(scandir($path) as $name)
        {
            // exclude excludes and hide index.php for the root
            if ($name === '.' || (($name === "index.php" || $name === "..") && $path === $base))
            {
                continue;
            }

            // check if file is hidden
            if ($this->isHidden($name))
            {
                continue;
            }

            $file = realpath($path . "/" . $name);
            $modified = date("Y-m-d H:i", filemtime($file));

            $is_folder = is_dir($file);

            array_push($files, ["name" => $name, "isFolder" => $is_folder, "icon" => $is_folder ? Icons::getFolderIcon() : Icons::getIcon(pathinfo($file, PATHINFO_EXTENSION)), "lastModified" => $modified, "size" => filesize($file)]);
        }

        return $files;
    }

    private function isHidden(string $path): bool
    {
        // files to hide, could array_merge with hidden files from a config
        $hidden = ["_internal", "vendor"];

        foreach ($hidden as $search)
        {
            if (strpos($path, $search) !== false)
            {
                return true;
            }
        }

        return false;
    }

    public function render(string $directory): string
    {
        if (($files = $this->readDirectory($directory)) === false)
        {
            http_response_code(404);

            return $this->twig->render("404.html.twig", ["title" => "Not found"]);
        }

        $directory = str_replace('\\', '/', $directory);
        if ($directory != '' && !str_ends_with($directory, '/'))
        {
            $directory .= '/';
        }

        $title = basename($directory); // TODO: If root folder, should display 'Home' or smth
        $readme = null;
        foreach ($files as $f)
        {
            if (strtoupper($f["name"]) === 'README.MD')
            {
                $parsedown = new ParsedownExtension();
                $parsedown->setSafeMode(true);
                $readme = $parsedown->text(file_get_contents($directory . $f["name"]));
                if ($parsedown->getTitle() !== null)
                {
                    $title = $parsedown->getTitle();
                }
                break;
            }
        }

        return $this->twig->render("index.html.twig",
            [
                "files" => $files,
                "title" => $title,
                "directory" => $directory,
                "readme" => $readme,
                "path" => array_filter(explode('/', $directory))
            ]
        );
    }
}
