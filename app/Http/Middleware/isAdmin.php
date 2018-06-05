<?php

namespace App\Http\Middleware;

use Closure;
use JWTAuth;
class isAdmin
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
            $user = JWTAuth::toUser($request->input("token"));
            if($user->type == "admin") {
                return $next($request);
            }else{
                return response()->json(["message" => "Unauthorized access to resource"], 403);
            }
        } catch (Exception $e) {
            return response()->json(["message" => $e->getMessage()], 500);
        }
    }
}
