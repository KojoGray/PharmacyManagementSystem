<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AdminOnlyMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    { 
        
        $token = $request->header("token");
        $userId = $request->header("userId");
        $userRole = $request->header("role");

       if(!$this->isAuthorized($token, $request,$userRole) ){
                return abort(401,"you don't have permission to access this data");
       }
       return $next($request);
    
    }

  public function isAuthorized($token, $userId,$role){
         if(!$token || !$userId || $role !== "admin" ){
                   return false;
         }
         return true;
  }
       // return $next($request);
    
}
