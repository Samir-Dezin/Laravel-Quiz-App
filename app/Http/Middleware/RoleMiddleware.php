<?php
namespace App\Http\Middleware;  

use Closure;  
use Illuminate\Http\Request;  
use Illuminate\Support\Facades\Auth;  

class RoleMiddleware  
{  
    /**  
     * Handle an incoming request.  
     *  
     * @param  \Illuminate\Http\Request  $request  
     * @param  \Closure  $next  
     * @param  string  $role  
     * @return mixed  
     */  
    public function handle(Request $request, Closure $next, string $role)  
    {  
        if (!Auth::check()) {  
            return response()->json(['error' => 'You are not authenticated'], 401);  
        }  

        if (!Auth::user()->hasRole($role)) {  
            return response()->json(['error' => 'You do not have the required role'], 403);  
        }  

        return $next($request);  
    }  
}