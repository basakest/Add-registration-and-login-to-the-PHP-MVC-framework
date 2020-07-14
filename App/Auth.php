<?php
namespace App;

class Auth
{
    /**
     * user login
     *
     * @param [object] $user
     * @return void
     */
    public static function login($user)
    {
        session_regenerate_id(true);
        $_SESSION['user_id'] = $user->id;
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
    }

    /**
     * judge if the user has logged in
     *
     * @return boolean
     */
    public static function isLoggedIn()
    {
        return isset($_SESSION['user_id']);
    }
}