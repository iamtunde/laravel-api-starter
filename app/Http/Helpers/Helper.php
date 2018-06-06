<?php

namespace App\Http\Helpers;

class Helper {

    public function formatErrors($messages)
    {
        $errors = [];
        foreach ($messages as $key => $value) {
            array_push($errors, $value);
        }

        return $errors;
    }

    public function saveExpiryTime($token)
    {
        //get current time in hours
        $currentTime = date("H:i");

        //add 20 mins to the current time to make the link expire after 20mins
        //the timeout is configurable with env variables, you can just replace
        //the value with the variable of your choice.
        $expiryTime = date('H:i',strtotime('+20 minutes',strtotime($currentTime)));

        //store the values in an array
        $timeData = [
            'token' => $token,
            'time'  => $expiryTime,
        ];

        return $timeData;
    }
}