<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerifyApiSecretToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $expectedToken = env('API_SECRET_TOKEN');

        // Check if the expected token is configured
        if (empty($expectedToken)) {
            return response()->json(['message' => 'API Secret Token is not configured.'], 500);
        }

        $providedToken = $request->header('X-Api-Token') ?: $request->query('api_token');

        // Allow bypassing if it's a preflight OPTIONS request
        if ($request->isMethod('OPTIONS')) {
            return $next($request);
        }

        if ($providedToken !== $expectedToken) {
            return response()->json(['message' => 'Unauthorized. Invalid API Token.'], 401);
        }

        return $next($request);
    }
}
