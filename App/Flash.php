<?php
namespace App;

class Flash
{
    const SUCCESS = 'success';
    const INFO = 'inof';
    const WARNING = 'warning';
    /**
     * add flash message to the session
     *
     * @param [string] $message
     * @return void
     */
    public static function addMessage($message, $type = 'success')
    {
        if (!isset($_SESSION['flash_notifications'])) {
            $_SESSION['flash_notifications'] = [];
        }
        $_SESSION['flash_notifications'][] = ['body' => $message, 'type' => $type];
        //var_dump($_SESSION);exit();
    }

    /**
     * get the flash messages
     *
     * @return array
     */
    public static function getMessages()
    {
        //if isset $_SESSION['flash_notifications'], execute the code block
        if (isset($_SESSION['flash_notifications'])){
            //give $message the value of $_SESSION['flash_notifications']
            $messages = $_SESSION['flash_notifications'];
            //clear $_SESSION['flash_notifications']
            unset($_SESSION['flash_notifications']);
            //return $message
            return $messages;
        }
    }
}