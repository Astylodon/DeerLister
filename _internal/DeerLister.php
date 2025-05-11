<?php

require_once "Icons.php";
require_once "ParsedownExtension.php";

use Twig\Loader\FilesystemLoader;
use Twig\Environment;
use Twig\TwigFilter;
use Yosymfony\Toml\Toml;

class DeerLister
{
    private Environment $twig;

    private array $filePreviews;
    private array $fileDisplays;
    private array $config;

    function __construct()
    {
        $this->filePreviews = [];
        $this->fileDisplays = [];
        $this->config = [];

        // setup twig
        $loader = new FilesystemLoader("_internal/templates");

        $this->twig = new Environment($loader);

        // load config
        if (file_exists("_internal/config.toml"))
        {
            $this->config = Toml::Parse(file_get_contents("_internal/config.toml"));
        }

        // Convert a size in byte to something more diggest
        $this->twig->addFilter(new TwigFilter("humanFileSize", function($size) {
            $units = ["B", "KB", "MB", "GB"];
            for ($i = 0; $size > 1024 && $i < count($units); $i++) $size /= 1024;

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

        // Can the file be rendered by the current display
        $this->twig->addFilter(new TwigFilter("canBeDisplayed", function($ext, $displayName) {
            return $this->fileDisplays[$displayName]->doesHandle($ext);
        }));
    }

    private function filesCmp(array $a, array $b): int
    {
        return strcmp(strtoupper($a["name"]), strtoupper($b["name"]));
    }

    /**
     * Sort a list of files and folders, will put folders first
     * 
     * @param array $files The array of files
     * 
     * @return array The sorted array
     */
    private function sortFiles(array $files): array
    {
        $folders = array_filter($files, function($file) {
            return $file["isFolder"];
        });

        $files = array_filter($files, function($file) {
            return !$file["isFolder"];
        });

        usort($folders, array($this, "filesCmp"));  
        usort($files, array($this, "filesCmp"));  

        return array_merge($folders, $files);
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

        if ($this->isHidden($directory, true))
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
            if ($this->isHidden($name, false))
            {
                continue;
            }

            $file = realpath($path . "/" . $name);
            $modified = date("Y-m-d H:i", filemtime($file));

            $isFolder = is_dir($file);
            $ext = pathinfo($file, PATHINFO_EXTENSION);

            array_push($files,
                [
                    "name" => $name,
                    "isFolder" => $isFolder,
                    "icon" => $isFolder ? Icons::getFolderIcon() : Icons::getIcon($ext),
                    "lastModified" => $modified,
                    "size" => filesize($file),
                    "extension" => $ext,

                    "filePreview" => !$isFolder && $this->isFilePreviewable($name) ? $this->pathCombine($relPath, $name) : null
                ]
            );
        }

        return $this->sortFiles($files);
    }

    /**
     * Returns if a file/folder should be displayed or not
     *
     * @param string $path Path to the file/folder
     * @param bool $ignoreHide Do we only consider forbidden files (true) or also hidden ones (false)
    */
    private function isHidden(string $path, bool $ignoreHide = false): bool
    {
        $config = $this->config;

        if (array_key_exists("forbidden", $config) && $config["forbidden"] !== NULL)
        {
            $forbidden = $config["forbidden"];
        }
        else
        {
            $forbidden = ["_internal", "vendor"];
        }

        foreach ($forbidden as $search)
        {
            if (strpos($path, $search) !== false)
            {
                return true;
            }
        }

        if (!$ignoreHide && array_key_exists("hidden", $config) && $config["hidden"] !== NULL)
        {

            foreach ($config["hidden"] as $search)
            {
                if ($path === $search)
                {
                    return true;
                }
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
        if ($this->config["preview_fallback"] === true)
        {
            return true;
        }

        $ext = pathinfo($filename, PATHINFO_EXTENSION);

        foreach ($this->filePreviews as $preview)
        {
            if ($preview->doesHandle($filename, $ext))
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
    public function registerFilePreview(string $name, string $preview)
    {
        // check if preview is enabled
        if (isset($this->config["previews"]) && isset($this->config["previews"]["enabled"]) && !in_array($name, $this->config["previews"]["enabled"]))
        {
            return;
        }

        $instance = new $preview($this->config);

        array_push($this->filePreviews, $instance);
    }

    /**
     * Registers a new file display
     * 
     * @param string $name The name of the file display
     * @param FilePreview $instance An instance of the file display class
     */
    public function registerFileDisplay(string $name, string $display)
    {
        $instance = new $display($this->config);

        $this->fileDisplays[$name] = $instance;
    }

    public function render(string $directory, string $preview): string
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
        $filesFilter = [];
        $displayMode = null;
        $displayBack = true;
        $displayOthers = true;

        // Check the config to set the display mode and others options set
        if (isset($this->config["displays"]))
        {
            foreach ($this->config["displays"] as $elem)
            {
                if ($elem["path"] === $path && array_key_exists($elem["format"], $this->fileDisplays))
                {
                    $displayMode = $elem["format"];
                    $displayBack = $elem["displayBack"] ?? false;
                    $displayOthers = $elem["displayOthers"] ?? false;
                    break;
                }
            }
        }

        foreach ($files as $f)
        {
            // We look for the README to display it
            if ($readme === null && $this->config["enabled_readme"] && strtoupper($f["name"]) === 'README.MD')
            {
                $content = file_get_contents(($directory == "" ? "" : $directory . "/") . $f["name"]);

                $parsedown = new ParsedownExtension();
                $parsedown->setSafeMode(true);
                $readme = $parsedown->text($content);
                if ($parsedown->getTitle() !== null)
                {
                    $title = $parsedown->getTitle();
                }
            }

            if ($f["name"] === $preview)
            {
                $title = $f["name"];
                $f["preview"] = true;
            }

            if (!$displayBack && $f["name"] === "..")
            { }
            else if (!$displayOthers && isset($displayMode) && !$this->fileDisplays[$displayMode]->doesHandle($f["extension"]))
            { }
            else
            {
                array_push($filesFilter, $f);
            }
        }

        return $this->twig->render("index.html.twig",
            [
                "files" => $filesFilter,
                "title" => $title,
                "path" => [ "full" => $path, "exploded" => array_filter(explode("/", $path)) ],
                "readme" => $readme,
                "display" => "displays/" . ($displayMode ?? "normal") . ".html.twig",
                "displayName" => $displayMode,
                "override" => [ "displayBack" => $displayBack, "displayOthers" => $displayOthers ]
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

        // check if file is not forbidden
        if ($this->isHidden($file, true))
        {
            http_response_code(404);

            return "File could not be previewed";
        }

        $filename = pathinfo($file, PATHINFO_BASENAME);
        $ext = pathinfo($file, PATHINFO_EXTENSION);

        foreach ($this->filePreviews as $preview)
        {
            if ($preview->doesHandle($filename, $ext))
            {
                return $preview->renderPreview($file, $ext, $this->twig);
            }
        }

        if ($this->config["preview_fallback"] === true)
        {
            return $this->twig->render("previews/default.html.twig");
        }

        http_response_code(404);
        return "File could not be previewed";
    }
}
