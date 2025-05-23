<?php

require_once "vendor/autoload.php";
require_once "_internal/DeerLister.php";

// autoload previews
spl_autoload_register(function($class) {
    $filePreview = "_internal/previews/" . $class . ".php";
    $fileDisplay = "_internal/displays/" . $class . ".php";

    if (file_exists($filePreview))
    {
        include $filePreview;
    }
    else if (file_exists($fileDisplay))
    {
        include $fileDisplay;
    }
});

$lister = new DeerLister();

$lister->registerFileDisplay("audio", AudioDisplay::class);
$lister->registerFileDisplay("image", ImageDisplay::class);

$lister->registerFilePreview("audio", AudioPreview::class);
$lister->registerFilePreview("image", ImagePreview::class);
$lister->registerFilePreview("video", VideoPreview::class);
$lister->registerFilePreview("code", CodePreview::class);
$lister->registerFilePreview("markdown", MarkdownPreview::class);
$lister->registerFilePreview("pdf", PdfPreview::class);

if (isset($_GET["preview"]))
{
    echo $lister->getFilePreview($_GET["preview"]);

    exit;
}

echo $lister->render($_GET["dir"] ?? "", $_GET["share"] ?? "");
