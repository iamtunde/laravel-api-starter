<?php

namespace App\Http\Controllers\Authentication;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Contracts\Auth\Guard;

use JWTAuth;
use Crypt;

use App\Http\Helpers\Helper;
use App\Http\Helpers\Validate;
use App\Http\Helpers\sendMail;
use App\Models\User;
use App\Models\Codes;
use App\Models\ResetPassword;
class LoadController extends Controller
{
    private $auth;
    private $user;
    private $helper;
    private $codes;
    private $validate;
    private $reset;
    private $mail;

    public function __construct(JWTAuth $auth, User $user, Helper $helper, Validate $validate, Codes $codes, ResetPassword $reset, sendMail $mail) {
        $this->auth = $auth;
        $this->user = $user;
        $this->helper = $helper;
        $this->codes = $codes;
        $this->reset = $reset;
        $this->validate = $validate;
        $this->mail = $mail;
    }

    public function loginUser(Request $request)
    {
        try {
            //get request body
            $data = $request->all();

            //validate the input
            $validation = $this->validate->auth($data, "login");

            if($validation->fails())
            {
                //format the error messages
                $errorMessages = $this->helper->formatErrors($validation->getMessageBag()->messages());

                //return validation error
                return response()->json(["message" => $errorMessages, "data" => $data, "error" => true], 403);
            } else {
                //check if the account is active
                $findUser = $this->user->where("email", $data["email"])->first();

                if($findUser !== null && $findUser->isDeleted == false)
                {
                    //when null proccess the authentication normally and create the auth token.
                    if(!$token = $this->auth::attempt($data))
                    {
                        //when authentication fails return false.
                        return response()->json(["data" => $data, "message" => "Incorrect login details", "error" => true], 401);
                    } else {
                        //get authenticated user's info.
                        $user = $this->auth::toUser($token);
            
                        //add token value to user object
                        $user->token = $token;

                        if($user->channel == null)
                        {
                            //add the user's avatar
                            if($user->avatar !== null)
                            {
                                $user->avatar = URL('/')."/uploads/avatar/".$user->avatar;
                            } else {
                                $user->avatar = URL('/')."/img/avatar.png";
                            }
                        }
            
                        //everything soft, return 200
                        return response()->json(["data" => $user, 'message' => "Login successfull", "error" => false], 200);
                    }
                } else {
                    //user account was deleted
                    return response()->json(["data" => $data, "message" => "User account not found", "error" => true], 401);
                }
            }
        } catch(\Exception $e) {
            return response()->json(["message" => $e->getMessage()], 500);
        }
    }

    public function createUser(Request $request)
    {
        try {
            //get request body
            $body = $request->all();

            //validate request body
            $validation = $this->validate->auth($body, null);

            if($validation->fails())
            {
                //format the error messages
                $errorMessages = $this->helper->formatErrors($validation->getMessageBag()->messages());

                //return validation error
                return response()->json(["message" => $errorMessages, "data" => $body, "error" => true], 403);
            } else {
                //encrypt password string
                $body["password"] = bcrypt($body["password"]);

                //store create the user info
                $this->user->create($body);

                //remove passwords from request body
                unset($body["password"]);
                unset($body["password_confirmation"]);

                //You can decide to enable the send confirmation mail feature
                //by removing the comment below this line
                $this->mail->welcomeMessage($body);

                //it's a beautiful day, don't you think
                return response()->json(["mesage" => "User successfully created", "data" => $body], 200);
            }
        } catch(\Exception $e) {
            return response()->json(["message" => $e->getMessage()], 500);
        }
    }

    public function getSignUpCode(Request $request)
    {
        try {
            //get request body
            $body = $request->all();

            //validate the request body
            $validation = $this->validate->auth($body, "code");

            if($validation->fails())
            {
                //format the error messages
                $errorMessages = $this->helper->formatErrors($validation->getMessageBag()->messages());

                //return validation error
                return response()->json(["message" => $errorMessages, "data" => $body, "error" => true], 403);
            } else {
                //generate a six digit code and add to request body
                $body["code"] = mt_rand(350000, 999999);

                //save the details
                $this->codes->create($body);

                //send code to the user
                $this->mail->sendSignUpCode($body);

                //it's a wonderful world
                return response()->json(["message" => "Sign-up code was sent successfully", "data" => $body], 200);
            }
        } catch(\Exception $e) {
            return response()->json(["message" => $e->getMessage()], 500);
        }
    }

    public function useSignUpCode(Request $request)
    {
        try {
            //get request body
            $body = $request->except('_token');

            //confirm sign up code
            $findCode = $this->codes->where("code", $body["code"])->first();

            if($findCode !== null) {
                if($findCode->used == false) {
                    //use the code
                    $this->codes->where($body)->update(["used" => true]);
                } else {
                    //this code has already been used
                    return response()->json(["message" => "Code already used", "data" => $body, "error" => true], 403);
                }
            } else {
                //code not found
                return response()->json(["message" => "Invalid code provided", "data" => $body, "error" => true], 404);
            }

            //smile! life is not hard.
            return response()->json(["message" => "Code verified successfully", "data" => $body, "error" => false], 200);
        } catch(\Exception $e) {
            return response()->json(["message" => $e->getMessage(), "error" => true], 500);
        }
    }

    public function sendPasswordRecoveryMail(Request $request)
    {
        try {
            //get request body
            $body = $request->all();

            //validate the input
            $validation = $this->validate->auth($body, "recovery-link");

            if($validation->fails()) {
                //format the error messages
                $errorMessages = $this->helper->formatErrors($validation->getMessageBag()->messages());

                //return validation error
                return response()->json(["message" => $errorMessages, "data" => $body, "error" => true], 403);
            } else {
                //find the email address
                $findEmail = $this->user->where("email", $body["email"])->first();
                
                if($findEmail !== null)
                {
                    //create a recovery token from the user's ID
                    $token = Crypt::encrypt($findEmail->id);

                    //prepare mail variables
                    $data = [
                        "email" => $findEmail->email,
                        "name" => $findEmail->fullName,
                        "url" => $request->target."?token=".$token,
                    ];

                    //create expiry time for password recovery
                    $expiry = $this->helper->saveExpiryTime($token);

                    //store the value in the database
                    $this->reset->create($expiry);

                    //send password recovery mail to user
                    $this->mail->sendPasswordRecoveryMail($data);
                } else {
                    //email not found bro
                    return response()->json(["data" => $body, "message" => "User not found for provided email", "error" => true], 403);
                }
            }
        } catch(\Exception $e) {
            return response()->json(["data" => [], "message" => $e->getMessage(), "error" => true], 500);
        }

        //sleep more. it's good for the body
        return response()->json(["data" => $body, "message" => "Password recovery link was sent sucessfully", "error" => false], 200);
    }

    public function resetPassword(Request $request)
    {
        try {
            //get request body
            $body = $request->all();

            //validate the user's input
            $validation = $this->validate->auth($body, "reset-password");

            if($validation->fails()) {
                //format the error messages
                $errorMessages = $this->helper->formatErrors($validation->getMessageBag()->messages());

                //return validation error
                return response()->json(["message" => $errorMessages, "data" => $body, "error" => true], 403);
            } else {
                //find a match for the recovery token
                $findToken = $this->reset->where("token", $body["token"])->first();

                if($findToken !== null)
                {
                    $currentTime = strtotime(date("H:i"));
                    
                    //check if token is expired or not
                    if(date("H:i", strtotime($findToken->time)) <= $currentTime)
                    {
                        //decrypt the token
                        $userId = Crypt::decrypt($findToken->token);
                        
                        //encrypt the new password
                        $newPassword = bcrypt($body["password"]);

                        //save the new password
                        $this->user->where("id", $userId)->update(["password" => $newPassword]);

                        //mark the token as used
                        $this->reset->where("token", $findToken->token)->update(["used" => true]);

                        //remove passwords string from object
                        unset($body["password"]);
                        unset($body["password_confirmation"]);
                    } else {
                        //expired token
                        return response()->json(["message" => "Password reset link expired", "data" => $body, "error" => true], 403);
                    }
                } else {
                    //wrong token bro.
                    return response()->json(["message" => "Invalid token provided", "data" => $body, "error" => true], 404);
                }
            }
        } catch(\Exception $e) {
            return response()->json(["message" => $e->getMessage(), "error" => true], 500);
        }

        //don't be unsociable, connect with friends and family more often
        return response()->json(["message" => "Password successfully changed", "data" => [], "error" => false], 200);
    }
}