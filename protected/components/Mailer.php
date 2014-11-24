<?php
/**
 * Created by PhpStorm.
 * User: ExiRouS
 * Date: 7/1/2014
 * Time: 12:27
 */

class Mailer {

    public static function send($to, $subject, $text)
    {
        $headers  = 'MIME-Version: 1.0' . "\r\n";
        $headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";
        //$headers .= 'To: '.$to . "\r\n";
        $headers .= 'From: LuftWaffeSchule <luftwaffeschule@gmail.com>' . "\r\n";
        mail($to, $subject, $text, $headers);
    }
} 