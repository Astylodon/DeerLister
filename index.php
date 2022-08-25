<?php

require_once './vendor/autoload.php';

use Twig\Loader\FilesystemLoader;
use Twig\Environment;

$loader = new FilesystemLoader('_internal/templates');
$twig = new Environment($loader);

$template = $twig->load('index.html.twig');

$data = [];
foreach (scandir(".") as $file)
{
    if ($file !== '..' && $file != '.' && $file != '_internal')
    {
        array_push($data, ['name' => $file, 'icon' => null, 'lastModified' => null, 'size' => filesize($file)]);
    }
}

echo $template->render(['files' => $data, 'title' => 'Deer Lister']);