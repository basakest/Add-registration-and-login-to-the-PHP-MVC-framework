<?php
namespace App;

//use Mailgun\Mailgun;
use \App\Config;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class Mail
{   /*
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
    */

    public static function send($to, $subject, $content)
    {
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            //$mail->SMTPDebug = 2;
            $mail->Host = Config::EMAIL_HOST;
            $mail->SMTPAuth = true;
            $mail->Username = Config::EMAIL_USER;
            $mail->Password = Config::EMAIL_PWD;
            $mail->SMTPSecure = "ssl";
            $mail->Port = 465;
            $mail->CharSet = 'UTF-8';

            $mail->IsHTML(true);
            $mail->setFrom("1652759879@qq.com");
            $mail->addAddress($to);
            //$mail->addReplyTo($email);
            $mail->Subject = $subject;
            $mail->Body = $content;
            $mail->send();
            $sent = true;
        } catch (Exception $e) {
            $errors[] = $mail->ErrorInfo;
        }
    }
}