<?php

namespace App\Http\Controllers;

use App\Models\Slider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class SliderController extends Controller
{

    public function index()
    {
        $data=Slider::get();
        return view('sliders', ['data' => $data]);
    }


    public function create()
    {
        return view('slider_add');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $input = $request->all();
        $rules = array(
            'name'      => 'required',
            'action'      => 'required',
            'image' => 'required'
        );

        $validator = Validator::make($input, $rules);


        if (!$validator->passes()) {
            return back()->with('error', 'Incomplete request. Kindly check and try again');
        }

        $storage=Storage::put('public/sliders', $input['image']);
        $link=Storage::url($storage);
        $input['image']=explode("/", $link)[3];
        Slider::create($input);

        return redirect()->route('sliders.index')->with('success', 'Slider created successfully');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id)
    {
        $slider=Slider::find($id);

        if (!$slider) {
            return back()->with('error', 'Invalid ID provided');
        }

        $slider->status=$slider->status == 1 ? 0 : 1;
        $slider->save();

        return redirect()->route('sliders.index')->with('success', 'Slider updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        $slider=Slider::find($id);

        if (!$slider) {
            return back()->with('error', 'Invalid ID provided');
        }

        $slider->delete();

        return redirect()->route('sliders.index')->with('success', 'Slider removed successfully');
    }
}
