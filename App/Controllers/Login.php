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

    public function before()
    {
        return true;
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
            //var_dump(isset($_SESSION['user_id']));
            //exit();
            $this->redirect('/');
            exit();
        } else {
            View::renderTemplate('Login/new.html', [
                'email' => $_POST['email']
            ]);
        }
    }

    public function destroyAction()
    {
        Auth::logout();
        $this->redirect('/');
    }
}