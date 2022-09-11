<?php

require_once "Icons.php";
require_once "ParsedownExtension.php";

use Twig\Loader\FilesystemLoader;
use Twig\Environment;
use Twig\TwigFilter;

class DeerLister
{
    private Environment $twig;

    private array $filePreviews;
    private array $config;

    function __construct()
    {
        $this->filePreviews = [];
        $this->config = [];

        // setup twig
        $loader = new FilesystemLoader("_internal/templates");

        $this->twig = new Environment($loader);

        // load config
        if (in_array("yaml", get_loaded_extensions()) && file_exists("_internal/config.yaml"))
        {
            $this->config = yaml_parse(file_get_contents("_internal/config.yaml"));
        }

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

        $relPath = $this->getRelativePath($directory);
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

            array_push($files,
                [
                    "name" => $name,
                    "isFolder" => $isFolder,
                    "icon" => $isFolder ? Icons::getFolderIcon() : Icons::getIcon(pathinfo($file, PATHINFO_EXTENSION)),
                    "lastModified" => $modified,
                    "size" => filesize($file),

                    "filePreview" => !$isFolder && $this->isFilePreviewable($name) ? $this->pathCombine($relPath, $name) : null
                ]
            );
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

    /**
     * Combines multiple values to a path. Currently very simple, does not fix paths or check for trailing path seperator
     * 
     * @param string $paths Parameters of paths
     * 
     * @return string The combined path
     */
    private function pathCombine(string ...$paths): string
    {
        return implode("/", array_diff($paths, [""]));
    }

    /**
     * Returns whether a file is previewable by one of the file previews
     * 
     * @param string $filename The name of the file
     * 
     * @return bool Whether the file is previewable
     */
    private function isFilePreviewable(string $filename): bool
    {
        foreach ($this->filePreviews as $preview)
        {
            if ($preview->doesHandle($filename))
            {
                return true;
            }
        }

        return false;
    }

    /**
     * Registers a new file preview
     * 
     * @param string $name The name of the file preview
     * @param FilePreview $instance An instance of the file preview class
     */
    public function registerFilePreview(string $name, FilePreview $instance)
    {
        // check if preview is enabled
        if (isset($this->config["previews"]) && !in_array($name, $this->config["previews"]))
        {
            return;
        }

        array_push($this->filePreviews, $instance);
    }

    public function render(string $directory): string
    {
        // read the directory
        if (($files = $this->readDirectory($directory, $this->config)) === false)
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

    public function getFilePreview(string $file): string
    {
        // make sure we are not accessing files outside web root
        // path passed to any file preview should already be safe
        $base = getcwd();
        $path = realpath($base . "/" . $file);

        if ($path === false || strpos($path, $base) !== 0)
        {
            http_response_code(404);

            return "File could not be previewed";
        }

        // TODO check config forbidden

        foreach ($this->filePreviews as $preview)
        {
            $filename = pathinfo($file, PATHINFO_BASENAME);

            if ($preview->doesHandle($filename))
            {
                return $preview->renderPreview($file, $this->twig);
            }
        }

        http_response_code(404);
        return "File could not be previewed";
    }
}
