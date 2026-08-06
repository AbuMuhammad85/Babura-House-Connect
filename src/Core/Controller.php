<?php

namespace App\Core;

class Controller
{
    public string $layout = 'main';

    public function setLayout(string $layout): void
    {
        $this->layout = $layout;
    }

    public function render(string $view, array $data = []): string
    {
        extract($data);

        ob_start();
        $viewFile = dirname(__DIR__) . "/Views/{$view}.php";
        if (file_exists($viewFile)) {
            include $viewFile;
        } else {
            echo "View file not found: " . $view;
        }
        $content = ob_get_clean();

        ob_start();
        $layoutFile = dirname(__DIR__) . "/Views/layouts/{$this->layout}.php";
        if (file_exists($layoutFile)) {
            include $layoutFile;
        } else {
            echo $content;
        }
        return ob_get_clean();
    }
}
