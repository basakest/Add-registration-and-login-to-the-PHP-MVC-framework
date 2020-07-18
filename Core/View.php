<?php

namespace Core;

/**
 * the view part
 */
class View
{
    /**
     * require the view file
     *
     * @param [string] $view
     * @return void
     */
    public static function render($view, $args = [])
    {
        extract($args, EXTR_SKIP);
        $file = "../App/Views/$view";

        if (is_readable($file)) {
            //echo $file;
            require $file;
        } else {
            throw new \Exception("$file not found");
        }
    }

    /**
     * render a template with args
     *
     * @param [text] $template
     * @param array $args
     * @return void
     */
    public static function renderTemplate($template, $args = [])
    {
        echo static::getTemplate($template, $args);
    }

    /**
     * get a template with args
     *
     * @param [string] $template
     * @param array $args
     * @return void
     */
    public static function getTemplate($template, $args = [])
    {
        static $twig = null;
        if ($twig == null) {
            $loader = new \Twig\Loader\FilesystemLoader(dirname(__DIR__) . "/App/Views");
            $twig = new \Twig\Environment($loader);
            $twig->addGlobal('current_user', \App\Auth::getUser());
            $twig->addGlobal('flash_messages', \App\Flash::getMessages());
        }
        return $twig->render($template, $args);
    }
}