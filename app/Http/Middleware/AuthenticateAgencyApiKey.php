<?php

namespace App\Http\Middleware;

use App\Models\AgencyApiKey;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthenticateAgencyApiKey
{
    public function handle(Request $request, Closure $next): Response
    {
        $key = $request->header('X-Agency-Api-Key');

        $apiKey = $key
            ? AgencyApiKey::where('api_key', $key)->where('is_active', true)->first()
            : null;

        if (! $apiKey) {
            return response()->json(['message' => 'Invalid or missing agency API key.'], 401);
        }

        $request->setUserResolver(fn () => $apiKey->agency);

        return $next($request);
    }
}
