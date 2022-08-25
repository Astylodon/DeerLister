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
        array_push($data, ['name' => $file, 'icon' => is_dir($file) ? 'fa-folder' : 'fa-file', 'lastModified' => null, 'size' => filesize($file)]);
    }
}

echo $template->render(['files' => $data, 'title' => 'Deer Lister']);