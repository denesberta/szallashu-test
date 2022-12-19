<?php

namespace App\Http\Middleware;

use App\Models\Device;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class DeviceAuthentication
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\JsonResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $apiKey = $request->header('X-API-KEY');

        if (!$apiKey) {
            return response()->json([
                'error' => 'Missing API key',
            ], 400);
        }

        $device = Device::where('api_key', $apiKey)->first();

        if (!$device) {
            return response()->json([
                'error' => 'Invalid API key',
            ], 400);
        }

        return $next($request);
    }
}
