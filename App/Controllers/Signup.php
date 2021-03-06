<?php

namespace App\Controllers;

use \Core\View;
use \App\Models\User;

class Signup extends \Core\Controller
{
    public function newAction()
    {
        View::renderTemplate('Signup/new.html');
    }

    public function createAction() {
        $user = new User($_POST);
        if ($user->save()) {
            $user->sendActivationEmail();
            $this->redirect('/signup/success');
            exit();
        } else {
            View::renderTemplate('Signup/new.html', [
                'user' => $user
            ]);
        }
    }

    public function successAction()
    {
        View::renderTemplate('Signup/success.html');
    }

    public function activateAction()
    {
        $token = $this->route_params['token'];
        User::active($token);
        $this->redirect('/signup/actived');
    }

    public function activedAction()
    {
        View::renderTemplate('Signup/actived.html');
    }
}