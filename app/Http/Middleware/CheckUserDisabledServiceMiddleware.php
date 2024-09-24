<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckUserDisabledServiceMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse) $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next, $service)
    {
        $user = Auth::user();

        if ($service == "airtime" && $user->airtime == 0) {
            return response()->json(['success' => 0, 'message' => 'You dont have permission to perform this operation. Kindly contact support.']);
        }

        if ($service == "data" && $user->data == 0) {
            return response()->json(['success' => 0, 'message' => 'You dont have permission to perform this operation. Kindly contact support.']);
        }

        if ($service == "tv" && $user->tv == 0) {
            return response()->json(['success' => 0, 'message' => 'You dont have permission to perform this operation. Kindly contact support.']);
        }

        if ($service == "education" && $user->education == 0) {
            return response()->json(['success' => 0, 'message' => 'You dont have permission to perform this operation. Kindly contact support.']);
        }

        if ($service == "electricity" && $user->electricity == 0) {
            return response()->json(['success' => 0, 'message' => 'You dont have permission to perform this operation. Kindly contact support.']);
        }

        if ($service == "airtime2cash" && $user->airtime2cash == 0) {
            return response()->json(['success' => 0, 'message' => 'You dont have permission to perform this operation. Kindly contact support.']);
        }

        if ($service == "wallet_transfer" && $user->wallet_transfer == 0) {
            return response()->json(['success' => 0, 'message' => 'You dont have permission to perform this operation. Kindly contact support.']);
        }

        return $next($request);
    }
}
