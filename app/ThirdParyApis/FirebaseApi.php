<?php

namespace App\ThirdParyApis;

class FirebaseApi{

    public function sendiOSNotification($deviceID, $titles, $message){
        $ch = curl_init("https://fcm.googleapis.com/fcm/send");
        //The device token.
        $token = $deviceID; //token here
        //Title of the Notification.
        $title = $titles;
        //Body of the Notification.
        $body = $message;
        //Creating the notification array.
        $notification = array('title' =>$title , 'text' => $body, 'sound'=>'default', "content_available"=>true);
        //This array contains, the token and the notification. The 'to' attribute stores the token.
        $arrayToSend = array('to' => $token, 'notification' => $notification,'priority'=>'high');
        //Generating JSON encoded string form the above array.
        $json = json_encode($arrayToSend);
        //Setup headers:
        $headers = array();
        $headers[] = 'Content-Type: application/json';
        $headers[] = 'Authorization: key= AIzaSyBvGkKWzgG0Ah-dw5EDlszZfX6Tiby67po'; // key here
        //Setup curl, add headers and post parameters.
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
        curl_setopt($ch, CURLOPT_HTTPHEADER,$headers);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER, true);
        //Send the request
        $response = curl_exec($ch);
        //Close request
        curl_close($ch);
    }

    function sendAndroidNotification($deviceId, $titles, $message){
        $registrationIds = array( $deviceId );
        $msg = array
        (
            'message'   => $message,
            'title'     =>$titles,
            'vibrate'   => 1,
            'sound'     => 1
        );
        $fields = array
        (
            'registration_ids'  => $registrationIds,
            'data'          => $msg
        );

        $headers = array
        (
            'Authorization: key= AAAAYXeBuFI:APA91bFidufG2_gC3OOZWz7y37FWQ0B-tIA1OdAa8lu4HYN4wfX8HbNZXa8Wxg76iWgD_VU4kmvAYu71aCeRPmn99jCsMP2f-BVgVhjRcLVypMFSVB5gKXcQS0Prk5088MIDSJ_mrs-E' ,
            'Content-Type: application/json'
        );

        $ch = curl_init();
        curl_setopt( $ch,CURLOPT_URL, 'https://android.googleapis.com/gcm/send' );
        curl_setopt( $ch,CURLOPT_POST, true );
        curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
        curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
        curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
        curl_setopt($ch,CURLOPT_RETURNTRANSFER, true);
        curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode( $fields ) );
        $result = curl_exec($ch );
        curl_close( $ch );
        // echo $result;
    }
}