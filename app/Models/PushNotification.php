<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PushNotification extends Model
{
    public static function pushNoti($tokens,$title,$description)
    {
        
        $serverKey = config('web_constant.firebase_server_key');

        $msg = array(
            'title' => $title,
            'description'  => $description,            
            'sound' => 'default',
        );

        $data = array(
            "click_action" => "FLUTTER_NOTIFICATION_CLICK",
            "description" => $description,
            "title" => $title
        );

        $fields = array(
            'registration_ids'  => $tokens,
            'notification'  => $msg,
            'data' => $data
        );

        $headers = array(
            'Authorization: key='. $serverKey,
            'Content-Type: application/json'
        );

        $ch = \curl_init();
        curl_setopt($ch, CURLOPT_URL, config('web_constant.firebase_url'));
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
        $result = curl_exec($ch);
        // if ($result === FALSE) {
        //     die('FCM Send Error: ' . curl_error($ch));
        // }

        curl_close($ch);

        return $result;
    }
}
