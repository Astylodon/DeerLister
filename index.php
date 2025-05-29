<?php

use DeerLister\DeerLister;
use DeerLister\Displays\AudioDisplay;
use DeerLister\Displays\ImageDisplay;
use DeerLister\Previews\AudioPreview;
use DeerLister\Previews\CodePreview;
use DeerLister\Previews\ImagePreview;
use DeerLister\Previews\MarkdownPreview;
use DeerLister\Previews\PdfPreview;
use DeerLister\Previews\VideoPreview;

require_once "vendor/autoload.php";

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
