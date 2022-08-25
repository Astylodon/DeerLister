<?php

require_once './vendor/autoload.php';

use Twig\Loader\FilesystemLoader;
use Twig\Environment;

$loader = new FilesystemLoader('_internal/templates');
$twig = new Environment($loader);

$template = $twig->load('index.html.twig');

$data = scandir(".");

echo $template->render(['files' => $data, 'title' => 'Deer Lister']);