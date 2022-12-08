<?php

namespace App\Http\Controllers;

use App\Models\FAQs;
use App\Models\Slider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class FAQsController extends Controller
{

    public function index()
    {
        $data=FAQs::get();
        return view('faqs', ['data' => $data]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
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
            'title'      => 'required',
            'content'      => 'required'
        );

        $validator = Validator::make($input, $rules);


        if (!$validator->passes()) {
            return back()->with('error', 'Incomplete request. Kindly check and try again');
        }

        FAQs::create($input);

        return redirect()->route('faqs.index')->with('success', 'FAQ added successfully');
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
        $faq=FAQs::find($id);

        if (!$faq) {
            return back()->with('error', 'Invalid ID provided');
        }

        return view('faq_edit', ['data' => $faq]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request)
    {
        $input=$request->all();
        $faq=FAQs::find($request->id);

        if (!$faq) {
            return back()->with('error', 'Invalid ID provided');
        }

        $faq->title=$input['title'];
        $faq->content=$input['content'];
        $faq->save();

        return redirect()->route('faqs.index')->with('success', 'FAQ updated successfully');
    }

    public function modify(Request $request, $id)
    {
        $faq=FAQs::find($id);

        if (!$faq) {
            return back()->with('error', 'Invalid ID provided');
        }

        $faq->status=$faq->status == 1 ? 0 : 1;
        $faq->save();

        return redirect()->route('faqs.index')->with('success', 'FAQ updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $faq=FAQs::find($id);

        if (!$faq) {
            return back()->with('error', 'Invalid ID provided');
        }

        $faq->delete();

        return redirect()->route('faqs.index')->with('success', 'FAQ removed successfully');
    }
}
