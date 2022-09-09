<?php

require_once "Icons.php";
require_once "ParsedownExtension.php";

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
                $path = $this->getRelativePath($directory . "/..");

                // don't create an url with ?dir if we are returning to root
                return $path == "" ? "/" : "?dir=" . $path;
            }

            $path = $file["isFolder"] ? "?dir=" : "/";
            
            return $path . $directory . ($directory == '' || str_ends_with($directory, '/') ? '' : '/') . $file["name"];
        }));

        // Build the path until the current index
        $this->twig->addFilter(new TwigFilter("buildPath", function($pathArray, $index) : string
        {
            $finalPath = "?dir=";

            foreach ($pathArray as $i => $value)
            {
                $finalPath .= $value . '/';
                if ($i === $index)
                {
                    break;
                }
            }

            return $finalPath;
        }));
    }

    private function filesCmp(array $a, array $b): int
    {
        if ($a["isFolder"] && $b["isFolder"])
        {
            return strcmp(strtoupper($a["name"]), strtoupper($b["name"]));
        }

        if ($b["isFolder"])
        {
            return 1;
        }

        return strcmp(strtoupper($a["name"]), strtoupper($b["name"]));
    }

    private function readDirectory(string $directory, mixed $config): array|false
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

        if ($this->isHidden($directory, $config, true))
        {
            return false;
        }

        $files = [];

        foreach(scandir($path) as $name)
        {
            // exclude current directory and index.php or parent for the root directory
            if ($name === '.' || (($name === "index.php" || $name === "..") && $path === $base))
            {
                continue;
            }

            // check if file is hidden
            if ($this->isHidden($name, $config, false))
            {
                continue;
            }

            $file = realpath($path . "/" . $name);
            $modified = date("Y-m-d H:i", filemtime($file));

            $isFolder = is_dir($file);

            array_push($files, ["name" => $name, "isFolder" => $isFolder, "icon" => $isFolder ? Icons::getFolderIcon() : Icons::getIcon(pathinfo($file, PATHINFO_EXTENSION)), "lastModified" => $modified, "size" => filesize($file)]);
        }

        usort($files, array($this, "filesCmp"));
        return $files;
    }

    /**
     * Returns if a file/folder should be displayed or not
     *
     * @param string $path Path to the file/folder
     * @param mixed $config config.yaml file
     * @param bool $ignoreHide Do we only consider forbidden files (true) or also hidden ones (false)
    */
    private function isHidden(string $path, mixed $config, bool $ignoreHide): bool
    {
        if (array_key_exists("forbidden", $config) && $config["forbidden"] !== NULL)
        {
            $hidden = $config["forbidden"];
        }
        else
        {
            $hidden = ["_internal", "vendor"];
        }
        if (!$ignoreHide && array_key_exists("hidden", $config) && $config["hidden"] !== NULL)
        {
            $hidden = [...$hidden, ...$config["hidden"]];
        }

        foreach ($hidden as $search)
        {
            if (strpos($path, $search) !== false)
            {
                return true;
            }
        }

        return false;
    }

    private function getRelativePath(string $directory): string
    {
        $base = getcwd();
        $path = realpath($base . "/" . $directory);
        
        return strtr(substr($path, strlen($base) + 1), DIRECTORY_SEPARATOR, "/");
    }

    public function render(string $directory): string
    {
        $config = in_array("yaml", get_loaded_extensions()) && file_exists("_internal/config.yaml") ? yaml_parse(file_get_contents("_internal/config.yaml")) : [];

        // read the directory
        if (($files = $this->readDirectory($directory, $config)) === false)
        {
            http_response_code(404);

            return $this->twig->render("404.html.twig", ["title" => "Not found"]);
        }

        // relative real path
        $path = $this->getRelativePath($directory);

        $title = $path == "" ? "Home" : basename($directory);
        $readme = null;
        foreach ($files as $f)
        {
            if (strtoupper($f["name"]) === 'README.MD')
            {
                $content = file_get_contents(($directory == "" ? "" : $directory . "/") . $f["name"]);

                $parsedown = new ParsedownExtension();
                $parsedown->setSafeMode(true);
                $readme = $parsedown->text($content);
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
                "path" => [ "full" => $path, "exploded" => array_filter(explode("/", $path)) ],
                "readme" => $readme
            ]
        );
    }
}
