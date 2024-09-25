<?php

namespace App\Http\Middleware;

use App\Models\Settings;
use Closure;
use Illuminate\Http\Request;

class ApiPlatformAccessMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse) $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {

        $device = $request->header('device') ?? $_SERVER['HTTP_USER_AGENT'];

        if (str_contains($device, "api") || str_contains($device, "Postman")) {
            $settings = Settings::where('name', 'api_enabled')->first();
            if ($settings->value != "1") {
                if (str_contains($settings->value, "0")) {
                    return response()->json(['success' => 0, 'message' => 'This medium is currently disabled. Kindly contact support.']);
                } else {
                    return response()->json(['success' => 0, 'message' => $settings->value]);
                }
            }
        } elseif (str_contains($device, "| true")) {
            $settings = Settings::where('name', 'app_enabled')->first();
            if ($settings->value != "1") {
                if (str_contains($settings->value, "0")) {
                    return response()->json(['success' => 0, 'message' => 'This medium is currently disabled. Kindly contact support.']);
                } else {
                    return response()->json(['success' => 0, 'message' => $settings->value]);
                }
            }
        } else {
            $settings = Settings::where('name', 'web_enabled')->first();
            if ($settings->value != "1") {
                if (str_contains($settings->value, "0")) {
                    return response()->json(['success' => 0, 'message' => 'This medium is currently disabled. Kindly contact support.']);
                } else {
                    return response()->json(['success' => 0, 'message' => $settings->value]);
                }
            }
        }

        return $next($request);
    }
}
