<?php
namespace App\Controllers;

abstract class Authenticated extends \Core\Controller
{
    protected function before()
    {
        //return true;
        $this->requireLogin();
    }
}