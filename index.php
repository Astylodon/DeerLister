<?php

require_once "vendor/autoload.php";
require_once "_internal/DeerLister.php";

// TODO see if either composer autoload or php audoload can be used for this
require_once "_internal/previews/MediaPreview.php";
require_once "_internal/previews/CodePreview.php";

$lister = new DeerLister();

$lister->registerFilePreview("media", new MediaPreview());
$lister->registerFilePreview("code", new CodePreview());

if (isset($_GET["preview"]))
{
    echo $lister->getFilePreview($_GET["preview"]);

    exit;
}

echo $lister->render($_GET["dir"] ?? "");
