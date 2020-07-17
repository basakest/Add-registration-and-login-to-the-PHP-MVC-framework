<?php
namespace App\Models;

use \App\Token;
use \PDO;
use \App\Models\User;

class RememberedLogin extends \Core\Model
{
    /**
     * find a rememberedLogin model by the token
     *
     * @param [string] $token
     * @return object 
     */
    public static function findByToken($token)
    {
        $token = new Token($token);
        $token_hash = $token->getHash();

        $sql = "select * from remembered_logins
                where token_hash = :token_hash";

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':token_hash', $token_hash, PDO::PARAM_STR);

        $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());

        $stmt->execute();

        return $stmt->fetch();
    }

    /**
     * get the user model
     *
     * @return object
     */
    public function getUser()
    {
        return User::findById($this->user_id);
    }

    public function hasExpired()
    {
        return strtotime($this->expires_at) < time();
    }

    /**
     * delete a record based on the token_hash
     *
     * @param [string] $token_hash
     * @return void
     */
    public function delete()
    {
        $sql = "delete from remembered_logins
                where token_hash = :token_hash";
        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':token_hash', $this->token_hash, PDO::PARAM_STR);
        $stmt->execute();
    }
}