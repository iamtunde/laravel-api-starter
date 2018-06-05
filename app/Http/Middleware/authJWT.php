<?php

namespace App\Http\Middleware;

use Closure;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Exceptions\JWTException;
class authJWT
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        try {
            //extract authorization token from request header
            $header = $request->header("Authorization");

            //check if the token structure is a OK
            if($header !== null)
            {
                if(strpos($header, "Bearer") !== false) {
                    $exploded = explode(" ", $header);
                    if($request->has("userId"))
                    {
                        //just continue nothing to see here
                    } else {
                        //convert the auth token to user
                        $user = JWTAuth::toUser($exploded[1]);
        
                        //add the user's ID to te request body
                        $request->merge(["userId" => $user->id]);
                    }
                } else {
                    return response()->json(["message" => "Incorrect authorization format, correct format is Bearer TokenValue"], 403);
                }
            } else {
                return response()->json(["message" => "Authorization header missing"], 403);
            }
        } catch (TokenExpiredException $e) {
            return response()->json(['message'=>'Token has expired'], 401);
        } catch (TokenInvalidException $e) {
            return response()->json(['message'=>'Token is invalid'], 401);
        } catch (JWTException $e) {
            return response()->json(['message'=> $e->getMessage()], 500);
        }

        return $next($request);
    }
}