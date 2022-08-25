<?php

require_once "vendor/autoload.php";
require_once "_internal/DeerLister.php";

$lister = new DeerLister();

echo $lister->render($_GET["dir"] ?? "");
