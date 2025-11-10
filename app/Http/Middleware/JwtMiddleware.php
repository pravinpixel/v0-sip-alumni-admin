<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use JWTAuth;
use Exception;
use Tymon\JWTAuth\Http\Middleware\BaseMiddleware;

class JwtMiddleware extends BaseMiddleware
{

    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */

    public function handle($request, Closure $next)
    {
        try {
            //$user = JWTAuth::parseToken()->authenticate();
            $route =$request->route()->getName();

            if($route == 'users.task.task.view'){
                $admin = Auth::user();
                if (!$admin) {
                    return $this->returnError('Dear User, You are not able to view the task details. The selected task has not been assigned to you!',423);
                }
            }else{
                $admin = Auth::user();
                if (!$admin) {
                    return $this->returnError('Invalid User',401);
                }
            }

            

            $token = JWTAuth::parseToken();
            $user = JWTAuth::authenticate($token);
            $claims = JWTAuth::getPayload($token)->toArray();

            $expectedIssuer = config('app.url');

            
            if($route == 'users.task.task.view'){
                if ($claims['iss'] !== $expectedIssuer) {
                    return $this->returnError("Dear User, You are not able to view the task details. The selected task has not been assigned to you!", 423);
                }
               
            }else{
                if ($claims['iss'] !== $expectedIssuer) {
                    return $this->returnError("Token is Invalid", 401);
                }
            }

        } catch (\Throwable $e) {
            if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenInvalidException) {
                if($route == 'users.task.task.view'){
                    return $this->returnError("Dear User, You are not able to view the task details. The selected task has not been assigned to you!", 423);
                }else{
                    return $this->returnError("Token is Invalid", 401);
                }
                
            } else if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenExpiredException) {
                return $this->returnError("Token is Expired", 403);
            } else {
                if($route == 'users.task.task.view'){
                    return $this->returnError("Dear User, You are not able to view the task details. The selected task has not been assigned to you!", 423);
                }else{
                    return $this->returnError("Authorization Token not found", 401);
                }
            }
        }


        return $next($request);
    }

    public function returnError($errors = false, $code)
    {
        return response([
            'success' => false,
            'message' => 'Error',
            'error' => $errors
        ], $code);
    }
}
