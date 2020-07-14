<?php
namespace App\Controllers;

use \Core\View;

class Item extends \App\Controllers\Authenticated
{
    public function indexAction()
    {
        View::renderTemplate('Item/index.html');
    }
}