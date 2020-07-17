<?php
namespace App\Controllers;

use Core\View;
use App\Models\User;
use App\Auth;
use App\Flash;

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
        $remember_me = isset($_POST['remember_me']);
        $user = User::authenticate($_POST['email'], $_POST['password']);
        if ($user) {
            Auth::login($user, $remember_me);
            Flash::addMessage('login successful');
            $this->redirect(Auth::getReturnToPage());
            exit();
        } else {
            Flash::addMessage('login unsuccessful, please try again', Flash::WARNING);
            View::renderTemplate('Login/new.html', [
                'email' => $_POST['email'],
                'remember_me' => $remember_me
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
        $this->redirect('/login/show-logout-message');
    }

    public function showLogoutMessageAction()
    {
        Flash::addMessage('logout successful');
        $this->redirect('/');
    }
}