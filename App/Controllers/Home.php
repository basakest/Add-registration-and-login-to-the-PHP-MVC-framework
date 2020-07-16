<?php

namespace App\Controllers;
use Core\View;

class Home extends \Core\Controller
{
    public function indexAction()
    {
        /*echo "this is a test message";
        View::render('Home/index.php', [
            'name' => 'alice',
            'colors' => ['red', 'green', 'blue']
        ]);*/

        View::renderTemplate('Home/index.html');
    }
}