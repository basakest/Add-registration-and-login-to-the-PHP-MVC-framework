<?php
namespace App\Controllers;

use Core\View;
use App\Models\User;
use App\Auth;

class Login extends \Core\Controller
{
    public function newAction()
    {
        View::renderTemplate('Login/new.html');
    }

    /**
     * user login
     *
     * @return void
     */
    public function createAction()
    {
        $user = User::authenticate($_POST['email'], $_POST['password']);
        if ($user) {
            Auth::login($user);
            $this->redirect(Auth::getReturnToPage());
            exit();
        } else {
            View::renderTemplate('Login/new.html', [
                'email' => $_POST['email']
            ]);
        }
    }

    /**
     * user logout
     *
     * @return void
     */
    public function destroyAction()
    {
        Auth::logout();
        $this->redirect('/');
    }
}