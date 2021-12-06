<?php

namespace App\Views;

use \Twig\Loader\FilesystemLoader;
use \Twig\Environment;


class View
{
    protected $twig;
    protected $loader;

    public function __construct()
    {
        $this->loader = new \Twig\Loader\FilesystemLoader(__DIR__ . '/templates');
        $this->twig = new \Twig\Environment($this->loader);
    }

    public function render($templateName, array $data)
    {
        echo $this->twig->render($templateName . '.twig', $data);
    }
}
