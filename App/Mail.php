<?php
namespace App;

use Mailgun\Mailgun;
use \App\Config;

class Mail
{
    public static function send($to, $subject, $text, $html)
    {
        # Instantiate the client.
        $mg = Mailgun::create(Config::MAILGUN_API_KEY); // For US servers
        $domain = Config::MAILGUN_DOMAIN;
        $params = array(
        'from'    => 'postmaster@' . Config::MAILGUN_DOMAIN,
        'to'      => $to,
        'subject' => $subject,
        'text'    => $text,
        'html'    => $html
        );

        # Make the call to the client.
        $mg->messages()->send($domain, $params);
    }
}