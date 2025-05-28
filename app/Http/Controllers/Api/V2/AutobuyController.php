<?php

namespace App\Http\Controllers\Api\V2;

use App\Http\Controllers\Controller;
use App\Models\Autobuy;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AutobuyController extends Controller
{
    // Add a new Autobuy
    public function store(Request $request)
    {
        $input = $request->all();
        $rules = array(
            'type' => 'required|in:data,airtime,cabletv,electricity',
            'provider' => 'required|string',
            'package' => 'nullable|string',
            'amount' => 'required|numeric',
            'number' => 'required|string',
            'frequency' => 'required|in:daily,weekly,monthly,yearly',
            'start_date' => 'required|date',
        );

        $validator = Validator::make($input, $rules);

        if (!$validator->passes()) {
            return response()->json(['success' => 0, 'message' => 'Some forms are left out', 'error' => $validator->errors()]);
        }

        // Calculate next_date based on frequency and start_date
        $startDate = Carbon::parse($input['start_date']);
        switch (strtolower($input['frequency'])) {
            case 'daily':
                $nextDate = $startDate->addDay();
                break;
            case 'weekly':
                $nextDate = $startDate->addWeek();
                break;
            case 'monthly':
                $nextDate = $startDate->addMonth();
                break;
            case 'yearly':
                $nextDate = $startDate->addYear();
                break;
            default:
                return response()->json(['success' => 0, 'message' => 'Invalid frequency value']);
        }

        $input['next_date'] = $nextDate->toDateString();
        $input['status'] = 1; // Default status to active
        $input['user_id'] = Auth::id();

        $autobuy = Autobuy::create($input);

        return response()->json(['success' => 1, 'message' => 'Autobuy created successfully', 'data' => $autobuy], 201);
    }

    // View all Autobuys
    public function index()
    {
        $autobuys = Autobuy::where('user_id', Auth::id())->get();
        return response()->json(['success' => 1, 'data' => $autobuys]);
    }

    // View past Autobuys by next_date
    public function pastAutobuys()
    {
        $autobuys = Autobuy::where('user_id', Auth::id())->where('next_date', '<', Carbon::now())->get();
        return response()->json(['success' => 1, 'data' => $autobuys]);
    }

    // View recent Autobuys by next_date
    public function recentAutobuys()
    {
        $autobuys = Autobuy::where('user_id', Auth::id())->where('next_date', '>=', Carbon::now())->get();
        return response()->json(['success' => 1, 'data' => $autobuys]);
    }

    // Cancel an Autobuy
    public function cancel($id)
    {
        // Find the Autobuy by ID and ensure it belongs to the authenticated user
        $autobuy = Autobuy::where('id', $id)->where('user_id', Auth::id())->first();

        if (!$autobuy) {
            return response()->json(['success' => 0, 'message' => 'Autobuy not found or unauthorized'], 404);
        }

        // Update the status to 0 (inactive)
        $autobuy->status = 0;
        $autobuy->save();

        return response()->json(['success' => 1, 'message' => 'Autobuy canceled successfully', 'data' => $autobuy]);
    }

    // Update an existing Autobuy
    public function update(Request $request, $id)
    {
        // Find the Autobuy by ID and ensure it belongs to the authenticated user
        $autobuy = Autobuy::where('id', $id)->where('user_id', Auth::id())->first();

        if (!$autobuy) {
            return response()->json(['success' => 0, 'message' => 'Autobuy not found or unauthorized'], 404);
        }

        // Validation rules for updating
        $rules = [
            'type' => 'sometimes|in:data,airtime,cabletv,electricity',
            'provider' => 'sometimes|string',
            'package' => 'nullable|string',
            'amount' => 'sometimes|numeric',
            'number' => 'sometimes|string',
            'frequency' => 'sometimes|in:daily,weekly,monthly,yearly',
            'start_date' => 'sometimes|date',
        ];

        $validator = Validator::make($request->all(), $rules);

        if (!$validator->passes()) {
            return response()->json(['success' => 0, 'message' => 'Validation failed', 'error' => $validator->errors()]);
        }

        // Update the Autobuy with the validated data
        $input = $request->all();

        if (isset($input['start_date']) && isset($input['frequency'])) {
            // Recalculate next_date if start_date or frequency is updated
            $startDate = Carbon::parse($input['start_date']);
            switch (strtolower($input['frequency'])) {
                case 'daily':
                    $nextDate = $startDate->addDay();
                    break;
                case 'weekly':
                    $nextDate = $startDate->addWeek();
                    break;
                case 'monthly':
                    $nextDate = $startDate->addMonth();
                    break;
                case 'yearly':
                    $nextDate = $startDate->addYear();
                    break;
                default:
                    return response()->json(['success' => 0, 'message' => 'Invalid frequency value']);
            }
            $input['next_date'] = $nextDate->toDateString();
        }

        $autobuy->update($input);

        return response()->json(['success' => 1, 'message' => 'Autobuy updated successfully', 'data' => $autobuy]);
    }
}
