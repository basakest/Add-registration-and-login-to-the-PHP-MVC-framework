<?php

namespace App\Controllers;

use \Core\View;
use \App\Models\User;

class Password extends \Core\Controller
{
    /**
     * display forgot.html
     *
     * @return void
     */
    public function forgotAction()
    {
        View::renderTemplate('Password/forgot.html');
    }

    public function requestResetAction()
    {
        User::sendPasswordReset($_POST['email']);
        View::renderTemplate('Password/reset-requested.html');
    }

    /**
     * if $token is right, diaplay reset.html
     *
     * @return void
     */
    public function resetAction()
    {
        $token = $this->route_params['token'];
        $user = $this->getUserOrExit($token);
        View::renderTemplate('Password/reset.html', ['token' => $token]);
    }

    public function resetPasswordAction()
    {
        $token = $_POST['token'];
        $user = $this->getUserOrExit($token);
        if ($user->resetPassword($_POST['password'])) {
            View::renderTemplate('Password/reset_success.html');
        } else {
            View::renderTemplate('Password/reset.html', [
                'token' => $token,
                'user' => $user
            ]);
        }
        
    }

    /**
     * get the user by token or show error message
     *
     * @param [string] $token
     * @return void
     */
    protected function getUserOrExit($token)
    {
        $user = User::findByPasswordReset($token);
        if ($user) {
            return $user;
        } else {
            View::renderTemplate('Password/token_expired.html');
            exit();
        }
    }
}