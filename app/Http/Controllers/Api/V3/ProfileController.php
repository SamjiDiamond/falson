<?php

namespace App\Http\Controllers\Api\V3;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ProfileController extends Controller
{
    public function securitySettings(Request $request)
    {
        $input = $request->all();
        $rules = array(
            'twofa' => 'required|int|in:0,1',
            'new_device_otp' => 'required|int|in:0,1',
        );

        $validator = Validator::make($input, $rules);

        $input = $request->all();

        if (!$validator->passes()) {
            return response()->json(['success' => 0, 'message' => implode(",", $validator->errors()->all())]);
        }

        User::where('id', Auth::id())->update([
            'twofa' => $input['twofa'],
            'new_device_otp' => $input['new_device_otp']
        ]);

        return response()->json(['success' => 1, 'message' => 'Security Updated Successfully',]);
    }
}
