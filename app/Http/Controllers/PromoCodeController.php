<?php

namespace App\Http\Controllers;

use App\Models\PromoCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PromoCodeController extends Controller
{
    /**
     * Display a listing of the promo codes.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $promoCodes = PromoCode::orderBy('created_at', 'desc')->paginate(10);
        return view('promo_codes.index', compact('promoCodes'));
    }

    /**
     * Store a newly created promo code in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'code' => 'required|unique:promo_codes|max:255',
            'type' => 'nullable|in:fixed,percentage',
            'reward_amount' => 'nullable|numeric|min:0',
            'max_redemptions' => 'nullable|integer|min:1',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after:start_date',
            'is_active' => 'nullable|boolean',
            'usage_criteria' => 'nullable|json',
        ]);

        if ($validator->fails()) {
            return redirect()->route('promo_codes.create')
                ->withErrors($validator)
                ->withInput();
        }

        PromoCode::create($request->all());

        return redirect()->route('promo_codes.index')
            ->with('success', 'Promo code created successfully.');
    }

    /**
     * Show the form for creating a new promo code.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('promo_codes.create');
    }

    /**
     * Show the form for editing the specified promo code.
     *
     * @param \App\Models\PromoCode $promoCode
     * @return \Illuminate\View\View
     */
    public function edit(PromoCode $promoCode)
    {
        return view('promo_codes.edit', compact('promoCode'));
    }

    /**
     * Update the specified promo code in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\PromoCode $promoCode
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, PromoCode $promoCode)
    {
        $validator = Validator::make($request->all(), [
            'code' => 'required|unique:promo_codes,code,' . $promoCode->id . '|max:255',
            'type' => 'nullable|in:fixed,percentage',
            'reward_amount' => 'nullable|numeric|min:0',
            'max_redemptions' => 'nullable|integer|min:1',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after:start_date',
            'is_active' => 'nullable|boolean',
            'usage_criteria' => 'nullable|json',
        ]);

        if ($validator->fails()) {
            return redirect()->route('admin.promo_codes.edit', $promoCode->id)
                ->withErrors($validator)
                ->withInput();
        }

        $promoCode->update($request->all());

        return redirect()->route('promo_codes.index')
            ->with('success', 'Promo code updated successfully.');
    }

    /**
     * Remove the specified promo code from storage.
     *
     * @param \App\Models\PromoCode $promoCode
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(PromoCode $promoCode)
    {
        $promoCode->delete();

        return redirect()->route('promo_codes.index')
            ->with('success', 'Promo code deleted successfully.');
    }
}
