<?php
namespace App;

class Token
{
    protected $token;

    /**
     * create a new random token
     */
    public function __construct($token_value = null)
    {
        if ($token_value) {
            $this->token = $token_value;
        } else {
            $this->token = bin2hex(random_bytes(16)); //16bytes = 128bites = 32 hex characters
        }
        
    }

    /**
     * get the value of the token
     *
     * @return void
     */
    public function getValue()
    {
        return $this->token;
    }

    /**
     * get the hashed value of the token
     *
     * @return void
     */
    public function getHash()
    {
        return hash_hmac('sha256', $this->token, \App\Config::SECRET_KEY); //sha256 = 64chars
    }
}