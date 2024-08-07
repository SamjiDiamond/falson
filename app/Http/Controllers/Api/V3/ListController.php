<?php

namespace App\Http\Controllers\Api\V3;

use App\Http\Controllers\Controller;
use App\Models\AppDataControl;

class ListController extends Controller
{
    public function dataCategory($network)
    {

        $datasets = AppDataControl::where([['network', '=', strtoupper($network)], ['status', 1], ['product_code', '!=', null]])->select('product_code')->distinct()->get();

        return response()->json(['success' => 1, 'message' => 'Fetch successfully', 'data' => $datasets]);
    }

    public function dataList($network, $category)
    {

        $datasets = AppDataControl::where([['network', '=', strtoupper($network)], ['product_code', $category], ['status', 1]])->select('name', 'coded', 'pricing as price', 'network', 'status')->orderby('dataplan', 'asc')->get();

        return response()->json(['success' => 1, 'message' => 'Fetch successfully', 'data' => $datasets]);
    }

}
