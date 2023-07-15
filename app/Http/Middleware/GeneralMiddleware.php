<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

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

        return $next($request);
    }
}
