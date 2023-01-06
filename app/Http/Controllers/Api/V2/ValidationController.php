<?php

namespace App\Http\Controllers\Api\V2;

use App\Http\Controllers\Api\ValidateController;
use App\Http\Controllers\Controller;
use App\Models\Settings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ValidationController extends Controller
{
    public function index(Request $request)
    {
        $input = $request->all();
        $rules = array(
            'service' => 'required',
            'provider' => 'required',
            'number' => 'required'
        );

        $validator = Validator::make($input, $rules);

        if (!$validator->passes()) {
            return response()->json(['success' => 0, 'message' => 'Required field(s) is missing']);
        }

        $s = new ValidateController();

        switch ($input['service']) {
            case "electricity":
                return $s->electricity_server1($input['number'], strtolower($input['provider']));
            case "tv":
                $sett=Settings::where('name', 'tv_server')->first();
                if($sett->value == 1) {
                    return $s->tv_server1($input['number'], strtolower($input['provider']));
                }else{
                    return $s->tv_server2($input['number'], strtolower($input['provider']));
                }
            default:
                return response()->json(['success' => 0, 'message' => 'Invalid service provided']);
        }
    }
}
