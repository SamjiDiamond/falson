<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

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
        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'success' => 0,
                'message' => 'Unauthorized'
            ]);
        }

        if ($user->pin_enabled == 1) {
            if (!$request->has('pin')) {
                return response()->json([
                    'success' => 0,
                    'message' => 'Pin is required to complete your transaction'
                ]);
            }

            if ($request->get('pin') != $user->pin) {
                return response()->json(['success' => 0, 'message' => 'Kindly provide valid Pin']);
            }
        }

        $lockKey = 'wallet_lock_user_' . $user->id;

        $lock = Cache::lock($lockKey, 10); // lock expires after 10 seconds

        if (!$lock->get()) {
            return response()->json([
                'success' => 0,
                'message' => 'Another transaction is currently processing. Please wait.'
            ]);
        }

        try {

            $response = $next($request);

        } finally {

            optional($lock)->release();

        }

        return $response;

    }
}
