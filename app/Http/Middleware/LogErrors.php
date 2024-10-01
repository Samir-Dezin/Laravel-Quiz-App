<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\ErrorLog;
use Illuminate\Support\Facades\Log;

class LogErrors
{
    public function handle($request, Closure $next)
    {
        try {
            // Process the request
            return $next($request);
        } catch (\Exception $e) {
            // Get the request log ID from the request attributes
            $requestLogId = $request->attributes->get('request_log_id');

            // Log the error in the database if requestLogId is present
            if ($requestLogId) {
                ErrorLog::create([
                    'request_id' => $requestLogId,
                    'error_message' => $e->getMessage(),
                    'error_file' => $e->getFile(),
                    'error_line' => $e->getLine(),
                    'error_trace' => $e->getTraceAsString(),
                ]);
            } else {
                // Optionally log this somewhere else if requestLogId is not found
                Log::error('No request log ID found for the error:', [
                    'error_message' => $e->getMessage(),
                    'error_file' => $e->getFile(),
                    'error_line' => $e->getLine(),
                ]);
            }

            // Rethrow the exception or return a custom response
            throw $e; // Rethrow the exception
        }
    }
}
