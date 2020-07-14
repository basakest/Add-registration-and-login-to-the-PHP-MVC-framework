<?php 
namespace App\Models;
use PDO;

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
            $password_hash = password_hash($this->password, PASSWORD_DEFAULT);
            $db = static::getDB();
            $sql = "insert into users(username ,email, password_hash)
                    values (:username, :email, :password_hash)";
            $stmt = $db->prepare($sql);
            $stmt->bindValue(':username', $this->username, PDO::PARAM_STR);
            $stmt->bindValue(':email', $this->email, PDO::PARAM_STR);
            $stmt->bindValue(':password_hash', $password_hash, PDO::PARAM_STR);
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

        if (static::emailExists($this->email)) {
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
    public static function emailExists($email)
    {
        return static::findByEmail($email) != false;
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
        if ($user) {
            if (password_verify($password, $user->password_hash)) {
                return $user;
            }
        }
    }
}