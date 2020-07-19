<?php

namespace App\Controllers;

use \Core\View;
use \App\Auth;

class Profile extends Authenticated
{
    protected function before()
    {
        parent::before();
        $this->user = Auth::getUser();
    }
    /**
     * display user profile page
     *
     * @return void
     */
    public function showAction()
    {
        View::renderTemplate('Profile/show.html');
    }

    /**
     * display the form to update user profile informaton
     *
     * @return void
     */
    public function editAction()
    {
        View::renderTemplate('Profile/edit.html', ['user' => $this->user]);
    }

    /**
     * update user profile information
     *
     * @return void
     */
    public function updateAction()
    {
        if ($this->user->updateProfileAction($_POST)) {
            View::renderTemplate('Profile/show.html');
        } else {
            View::renderTemplate('Profile/edit.html', ['user' => $this->user]);
        }
    }
}