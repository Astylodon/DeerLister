<?php

require_once "vendor/autoload.php";
require_once "_internal/DeerLister.php";

// autoload previews
spl_autoload_register(function($class) {
    $file = "_internal/previews/" . $class . ".php";

    if (file_exists($file))
    {
        include $file;
    }
});

$lister = new DeerLister();

$lister->registerFilePreview("audio", AudioPreview::class);
$lister->registerFilePreview("image", ImagePreview::class);
$lister->registerFilePreview("video", VideoPreview::class);
$lister->registerFilePreview("code", CodePreview::class);

if (isset($_GET["preview"]))
{
    echo $lister->getFilePreview($_GET["preview"]);

    exit;
}

echo $lister->render($_GET["dir"] ?? "");
