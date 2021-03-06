<?php

namespace Core;

use \App\Auth;
use \App\Flash;

/**
 * Base controller
 *
 * PHP version 5.4
 */
abstract class Controller
{

    /**
     * Parameters from the matched route
     * @var array
     */
    protected $route_params = [];

    /**
     * Class constructor
     *
     * @param array $route_params  Parameters from the route
     *
     * @return void
     */
    public function __construct($route_params)
    {
        $this->route_params = $route_params;
    }

    /**
     * call a function before and after an action
     *
     * @param [string] $name
     * @param [array] $args
     * @return void
     */
    public function __call($name, $args = [])
    {
        $method = $name . 'Action';
        if (method_exists($this, $method)) {
            if ($this->before() !== false) {
                call_user_func_array([$this, $method], $args);
                $this->after();
            } 
        }else {
            throw new \Exception("Method $method not found in controller " . get_class($this));
        }
    }

    /**
     * called before an action is executed
     *
     * @return void
     */
    protected function before()
    {
        
    }

    /**
     * called after an action is executed
     *
     * @return void
     */
    protected function after()
    {

    }

    public function redirect($url)
    {
        header('Location: http://' . $_SERVER['HTTP_HOST'] . $url, true, 303);
    }

    public function requireLogin()
    {
        if (!Auth::getUser()) {
            //session_start();
            Auth::rememberRequestedPage();
            Flash::addMessage('please login to access this page', Flash::INFO);
            //var_dump($_SESSION);
            //exit();
            $this->redirect('/login');
        }
    }
}
