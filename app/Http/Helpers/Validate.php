<?php

namespace App\Http\Helpers;

use Validator;

class Validate {
    private $validator;

    public function __construct(Validator $validator)
    {
        $this->validator = $validator;
    }

    public function auth($data, $type)
    {
        if($type == "code")
        {
            return $this->validator::make($data, ["email" => "required|unique:activationCodes|email"]);
        }elseif($type == "reset-password") {
            return $this->validator::make($data, [
                "token" => "required",
                "password" => "required|min:6|confirmed",
            ]);
        }elseif($type == "login") {
            return $this->validator::make($data, [
                "email" => "required",
                "password" => "required|min:6",
            ]);
        }elseif($type == "recovery-link") {
            return $this->validator::make($data, [
                "email" => "required",
                "target" => "required",
            ]);
        } else {
            return $this->validator::make($data, [
                "email" => "required|unique:users|email",
                "password" => "required|min:6|confirmed",
                "fullName" => "required",
                "phone" => "required|min:11|unique:users",
                "address" => "required",
                "type" => "required", 
            ]);
        }
    }
}