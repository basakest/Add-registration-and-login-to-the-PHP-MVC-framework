<?php
namespace App;

use \App\Models\User;
use \App\Models\RememberedLogin;

class Auth
{
    /**
     * user login
     *
     * @param [object] $user
     * @return void
     */
    public static function login($user, $remember_me)
    {
        session_regenerate_id(true);
        $_SESSION['user_id'] = $user->id;
        if ($remember_me) {
            if ($user->rememberLogin()) {
                setcookie('remember_me', $user->remember_token, $user->expiry_timestamp, '/');
            }
        }
    }

    /**
     * user logout
     *
     * @return void
     */
    public static function logout()
    {
        // Unset all of the session variables.
        $_SESSION = array();

        // If it's desired to kill the session, also delete the session cookie.
        // Note: This will destroy the session, and not just the session data!
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }

        // Finally, destroy the session.
        session_destroy();
        static::forgetLogin();
    }

    /**
     * remember the request page
     *
     * @return void
     */
    public static function rememberRequestedPage()
    {
        $_SESSION['return_to'] = $_SERVER['REQUEST_URI'];
    }

    /**
     * get the page to retrun or / 
     *
     * @return void
     */
    public static function getReturnToPage()
    {
        return $_SESSION['return_to'] ?? '/';
    }

    public static function getUser()
    {
        if (isset($_SESSION['user_id'])) {
            return User::findById($_SESSION['user_id']);
        } else {
            //echo 123;
            //exit();
            return static::loginFromRememberCookie();
        }
    }

    /**
     * login by the token cookie
     *
     * @return object the user object
     */
    public static function loginFromRememberCookie()
    {
        $cookie = $_COOKIE['remember_me'] ?? false;
        //var_dump($cookie);
        //exit();
        if ($cookie) {
            $remembered_login = RememberedLogin::findByToken($cookie);
            if ($remembered_login && !$remembered_login->hasExpired()) {
                $user = $remembered_login->getUser();
                static::login($user, false);
                return $user;
            }
        }
    }

    /**
     * delete the cookie and the record in database
     *
     * @return void
     */
    protected static function forgetLogin()
    {
        $cookie = $_COOKIE['remember_me'] ?? false;
        if ($cookie) {
            //echo 333;exit();
            $remembered_login = RememberedLogin::findByToken($cookie);
            if ($remembered_login) {
                $remembered_login->delete();
            }
            setcookie('remember_me', '', time() - 3600);
        }
    }
}