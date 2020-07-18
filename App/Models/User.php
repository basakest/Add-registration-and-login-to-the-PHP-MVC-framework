<?php 

namespace App\Models;

use PDO;
use \App\Token;
use \App\Mail;
use \Core\View;

/**
 * the User class
 */
class User extends \Core\Model
{
    public $password;
    public $username;
    public $email;
    public $password_confirmation;
    public $errors = [];

    /**
     * construce the User class
     *
     * @param [array] $data
     */
    public function __construct($data = [])
    {
        foreach ($data as $i => $v) {
            $this -> $i = $v;
        }
    }

    /**
     * create a user to the database
     *
     * @return boolean save successful or not
     */
    public function save()
    {
        $this->validate();
        if (empty($this->errors)) {
            $token = new Token();
            $this->activation_token = $token->getValue();
            $hashed_token = $token->getHash();
            $password_hash = password_hash($this->password, PASSWORD_DEFAULT);
            $db = static::getDB();
            $sql = "insert into users(username ,email, password_hash, activation_hash)
                    values (:username, :email, :password_hash, :activation_hash)";
            $stmt = $db->prepare($sql);
            $stmt->bindValue(':username', $this->username, PDO::PARAM_STR);
            $stmt->bindValue(':email', $this->email, PDO::PARAM_STR);
            $stmt->bindValue(':password_hash', $password_hash, PDO::PARAM_STR);
            $stmt->bindValue(':activation_hash', $hashed_token, PDO::PARAM_STR);
            return $stmt->execute();
        }
        return false;
    }

    /**
     * validate the information about an user
     *
     * @return void
     */
    public function validate()
    {
        if ($this->username === '') {
            $this->errors[] = 'username can\'t be null';
        }

        if (filter_var($this->email, FILTER_VALIDATE_EMAIL) === false)
        {
            $this->errors[] = 'email format wrong';
        }

        if (static::emailExists($this->email, $this->id ?? null)) {
            $this->errors[] = 'this email is already used';
        }

        if (strlen($this->password) < 6) {
            $this->errors[] = 'the length of password can\'t less than 6 characters';
        }

        if (preg_match('/.*[a-z]+.*/i', $this->password) == 0) {
            $this->errors[] = 'password must contain at least one character';
        }

        if (preg_match('/.*[0-9]+.*/i', $this->password) == 0) {
            $this->errors[] = 'password muat contain at least one number';
        }
    }

    /**
     * judge whether the email is already used
     *
     * @return boolean true if this email is used
     */
    public static function emailExists($email, $ignore_id = null)
    {
        $user = static::findByEmail($email);
        if ($user) {
            if ($ignore_id != $user->id) {
                return true;
            }
        }
        return false;
    }

    /**
     * get the object about an user based on the email
     *
     * @param [string] $email
     * @return [object] the user object
     */
    public static function findByEmail($email)
    {
        $sql = 'select * from users where email = :email';
        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':email', $email, PDO::PARAM_STR);
        $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());
        $stmt->execute();
        return $stmt->fetch();
    }

    /**
     * get current user by id
     *
     * @param [int] $id
     * @return object current user
     */
    public static function findById($id)
    {
        $sql = 'select * from users where id = :id';
        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());
        $stmt->execute();
        return $stmt->fetch();
    }

    /**
     * authenticate the user
     *
     * @param [string] $email
     * @param [string] $password
     * @return [object] the user object
     */
    public static function authenticate($email, $password)
    {
        $user = static::findByEmail($email);
        if ($user && $user->is_active) {
            if (password_verify($password, $user->password_hash)) {
                return $user;
            }
        }
    }

    /**
     * insert a record to remembered_login table
     *
     * @return boolean
     */
    public function rememberLogin()
    {
        $token = new Token();
        $hashed_token = $token->getHash();
        $this->remember_token = $token->getValue();
        $this->expiry_timestamp = time() + 60 * 60 * 24 * 30;
        $sql = "insert into remembered_logins(token_hash, user_id, expires_at)
                values(:token_hash, :user_id, :expires_at)";
        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':token_hash', $hashed_token, PDO::PARAM_STR);
        $stmt->bindValue(':user_id', $this->id, PDO::PARAM_INT);
        $stmt->bindValue(':expires_at', date('Y-m-d H:i:s', $this->expiry_timestamp), PDO::PARAM_STR);
        return $stmt->execute();
    }

    public static function sendPasswordReset($email)
    {
        $user = static::findByEmail($email);
        if ($user) {
            if ($user->startPasswordReset()) {
                $user->sendPasswordResetEmail();
            }
        }
    }

    /**
     * change the record in users to start password reset
     *
     * @return boolean
     */
    public function startPasswordReset()
    {
        $token = new Token();
        $token_hash = $token->getHash();
        $this->password_reset_token = $token->getValue();
        $expiry_timestamp = time() + 60 * 60 * 2;
        $sql = "update users
                set password_reset_hash = :token_hash,
                    password_expires_at = :expires_at
                where id = :id";
        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':token_hash', $token_hash, PDO::PARAM_STR);
        $stmt->bindValue(':expires_at', date('Y-m-d H:i:s', $expiry_timestamp), PDO::PARAM_STR);
        $stmt->bindValue(':id', $this->id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    /**
     * Send the reset password email to user
     *
     * @return void
     */
    public function sendPasswordResetEmail()
    {
        $url = 'http://' . $_SERVER['HTTP_HOST'] . '/password/reset/' . 
            $this->password_reset_token;
        //$text = View::getTemplate('Password/reset_email.txt', ['url' => $url]);
        $html = View::getTemplate('Password/reset_email.html', ['url' => $url]);
        Mail::send($this->email, 'Password reset', $html);
    }

    /**
     * find the user by the password reset
     *
     * @param [string] $token
     * @return void
     */
    public static function findByPasswordReset($token)
    {
        $token = new Token($token);
        $hashed_token = $token->getHash();
        $sql = "select * from users 
                where password_reset_hash = :token_hash";
        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':token_hash', $hashed_token, PDO::PARAM_STR);
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());
        $user = $stmt->fetch();
        if ($user) {
            if (strtotime($user->password_expires_at) > time()) {
                return $user;
            }
        }
    }

    /**
     * reset password
     *
     * @param [string] $password
     * @return void
     */
    public function resetPassword($password)
    {
        $this->password = $password;
        $this->validate();
        if (empty($this->errors)) {
            $password_hash = password_hash($password, PASSWORD_DEFAULT);
            $sql = "update users
                    set password_hash = :password_hash,
                    password_reset_hash = null,
                    password_expires_at = null
                    where id = :id";
            $db = static::getDB();
            $stmt = $db->prepare($sql);
            $stmt->bindValue(':password_hash', $password_hash, PDO::PARAM_STR);
            $stmt->bindValue(':id', $this->id, PDO::PARAM_INT);
            return $stmt->execute();
        }
        return false;
    }

    /**
     * Send the active email to user
     *
     * @return void
     */
    public function sendActivationEmail()
    {
        $url = 'http://' . $_SERVER['HTTP_HOST'] . '/signup/activate/' . 
            $this->activation_token;
        //$text = View::getTemplate('Password/reset_email.txt', ['url' => $url]);
        $html = View::getTemplate('Password/activation_email.html', ['url' => $url]);
        Mail::send($this->email, 'activation account', $html);
    }

    /**
     * active the user
     *
     * @param [string] $value
     * @return void
     */
    public static function active($value)
    {
        $token = new Token($value);
        $hashed_token = $token->getHash();
        $db = static::getDB();
        $sql = "update users
                set is_active = 1,
                    activation_hash = null
                where activation_hash = :hashed_token";
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':hashed_token', $hashed_token, PDO::PARAM_STR);
        $stmt->execute();
    }
}