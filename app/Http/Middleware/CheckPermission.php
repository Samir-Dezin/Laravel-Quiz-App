<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckPermission
{
    public function handle(Request $request, Closure $next, $permission)
    {
        if (!$request->user()->can($permission)) {
            return response()->json(['error' => 'Forbidden'], 403);
        }
        return $next($request);
    }
}
