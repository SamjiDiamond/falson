<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Slider;
use Illuminate\Http\Request;

class OthersController extends Controller
{
    function sliders(){
        $sliders=Slider::where('status', 1)->get();
        return response()->json(['success' => 1, 'message' => 'Fetched successfully', 'data' => $sliders, 'link'=>route('show.sliders', '')]);
    }
}
