<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\RequestLog;
use Illuminate\Support\Facades\Auth;

class LogRequests
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Store the request log in the database
        RequestLog::create([
            'ip_address' => $request->ip(),
            'method' => $request->method(),
            'url' => $request->fullUrl(),
            'request_payload' => json_encode($request->all()), // Log the request body/payload
            'user_id' => Auth::check() ? Auth::id() : null, // Store user ID if authenticated
        ]);

        // Proceed to the next middleware or the controller
        return $next($request);
    }
}
