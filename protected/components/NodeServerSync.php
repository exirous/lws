<?php

/**
 * Created by PhpStorm.
 * User: ExiRouS
 * Date: 7/6/2014
 * Time: 10:47
 */
class NodeServerSync
{

    public static function sendInternalMessage($event, $data = [])
    {
        self::_sendMessage($event, $data , "", true);
    }

    public static function sendMessage($event, $data ,$room)
    {
        self::_sendMessage($event, $data , $room, false);
    }

    private static function _sendMessage($event, $data, $room = "", $isInternal = false)
    {
        // URL on which we have to post data
        $url = "http://127.0.0.1:3010";
        // Any other field you might want to post
        $json_data = json_encode(["room" => $room, "event" => $event, "data" => $data, "isInternal" => $isInternal]);
        // Initialize cURL
        $ch = curl_init();
        // Set URL on which you want to post the Form and/or data
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $json_data);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: text/plain'));
        // Pass TRUE or 1 if you want to wait for and catch the response against the request made
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        // For Debug mode; shows up any error encountered during the operation
        curl_setopt($ch, CURLOPT_VERBOSE, 1);
        // Execute the request
        $response = curl_exec($ch);
        $error = curl_error($ch);
    }
} 