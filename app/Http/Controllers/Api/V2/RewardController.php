<?php

namespace App\Http\Controllers\Api\V2;

use App\Http\Controllers\Controller;
use App\Models\DailyCheckIn;
use App\Models\Spinwin;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

/**
 * @OA\Info(
 *     title="Your API",
 *     version="2.0"
 * )
 * @OA\SecurityScheme(
 *     securityScheme="bearerAuth",
 *     type="http",
 *     scheme="bearer"
 * )
 */
class RewardController extends Controller
{

    public function checkIn(Request $request)
    {
        $user = Auth::user();

        $today = Carbon::today();

        $existingCheckIn = DailyCheckIn::where('user_id', $user->id)
            ->whereDate('check_in_date', $today)
            ->first();

        if ($existingCheckIn) {
            return response()->json(['success' => 0, 'message' => 'You have already checked in today.'], 409);
        }

        DailyCheckIn::create([
            'user_id' => $user->id,
            'check_in_date' => $today,
        ]);

        return response()->json(['success' => 1, 'message' => 'Checked in successfully.']);
    }

    public function getCheckIns(Request $request)
    {
        $user = Auth::user();

        $checkIns = DailyCheckIn::where('user_id', $user->id)->orderBy('check_in_date', 'desc')->limit(30)->get();

        return response()->json(['success' => 1, 'message' => 'Fetch successfully', 'data' => $checkIns]);
    }


    public function fetch()
    {
        $data = Spinwin::where("status", 1)->inRandomOrder()->limit(10)->get();

        return response()->json(['success' => 1, 'message' => 'Fetched successfully', 'data' => $data]);
    }

    public function continue(Request $request)
    {
        $input = $request->all();
        $rules = array(
            'id' => 'required',
            'number' => 'required|string'
        );

        $validator = Validator::make($input, $rules);


        if (!$validator->passes()) {

            return response()->json(['success' => 0, 'message' => implode(",", $validator->errors()->all())]);
        }

        $spin = Spinwin::find($input['id']);


        if (!$spin) {
            return response()->json(['success' => 0, 'message' => 'Error detected in your request']);
        }

        if ($spin->status == 0) {
            return response()->json(['success' => 0, 'message' => 'This gift has been disabled']);
        }

        if ($spin->type == "empty") {
            return response()->json(['success' => 0, 'message' => 'No gift for you. Try again']);
        }

        if ($spin->qty < 1) {
            return response()->json(['success' => 0, 'message' => 'Gift quantity exceeded']);
        }

        return response()->json(['success' => 1, 'message' => 'Claimed Successfully']);
    }

}
