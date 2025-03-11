<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GeneralMiddleware
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
        $random_sleep_time = rand(1, 10); // Generate a random number between 1 and 10
        sleep($random_sleep_time); // Pause execution for the randomly generated number of seconds

        $user = Auth::user();

        if ($user->pin_enabled == 1) {
            if ($request->get('pin') == null) {
                return response()->json(['success' => 0, 'message' => 'Pin is required to complete your transaction']);
            }

            if ($request->get('pin') != $user->pin) {
                return response()->json(['success' => 0, 'message' => 'Kindly provide valid Pin']);
            }
        }

        return $next($request);
    }
}
