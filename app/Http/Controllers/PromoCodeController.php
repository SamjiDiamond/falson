<?php

namespace App\Http\Controllers;

use App\Models\PromoCode;
use App\Models\Settings;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
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
        $promoCodes = PromoCode::orderBy('id', 'desc')->paginate(20);
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
            'target' => 'required|in:all_users,single_user,resellers,top_users,top_resellers,admins_all,admins_specific,new_users',
            'amount' => 'required|numeric|min:0',
            'count' => 'nullable|integer|min:1',
            'user_name' => 'required_if:target,single_user',
            'admin_usernames' => 'required_if:target,admins_specific',
            'enabled' => 'required_if:target,new_users|in:0,1',
        ]);

        if ($validator->fails()) {
            return redirect()->route('promo_codes.create')
                ->withErrors($validator)
                ->withInput();
        }

        $target = $request->string('target')->toString();
        $amount = (float) $request->get('amount');
        $count = (int) ($request->get('count') ?? 0);

        try {
            DB::transaction(function () use ($request, $target, $amount, $count) {
                if ($target === 'new_users') {
                    $this->upsertSetting('enable_new_user_reward', (string) $request->get('enabled'));
                    $this->upsertSetting('new_user_reward_amount', (string) $amount);
                    return;
                }

                if ($target === 'all_users') {
                    $code = $this->generateUniquePromoCode();
                    PromoCode::create([
                        'code' => $code,
                        'amount' => $amount,
                        'used' => 0,
                        'reuseable' => 1,
                        'usedby' => '',
                        'generated_for' => 'all',
                    ]);
                    return;
                }

                $usernames = [];

                if ($target === 'single_user') {
                    $user = User::where('user_name', trim((string) $request->get('user_name')))->first();
                    if (!$user) {
                        throw new \RuntimeException('User not found');
                    }
                    $usernames = [$user->user_name];
                } elseif ($target === 'admins_all') {
                    $usernames = User::whereIn('status', ['admin', 'superadmin'])->pluck('user_name')->all();
                } elseif ($target === 'admins_specific') {
                    $raw = (string) $request->get('admin_usernames');
                    $usernames = array_values(array_filter(array_map('trim', preg_split('/[,\n]/', $raw) ?: [])));
                    $usernames = User::whereIn('user_name', $usernames)
                        ->whereIn('status', ['admin', 'superadmin'])
                        ->pluck('user_name')
                        ->all();
                } elseif ($target === 'top_users') {
                    $limit = $count > 0 ? $count : 10;
                    $usernames = User::select('tbl_agents.user_name', DB::raw('SUM(tbl_transactions.amount) as total_amount'))
                        ->join('tbl_transactions', 'tbl_agents.user_name', '=', 'tbl_transactions.user_name')
                        ->where('tbl_transactions.name', '!=', 'wallet funding')
                        ->groupBy('tbl_agents.user_name')
                        ->orderByDesc('total_amount')
                        ->limit($limit)
                        ->pluck('tbl_agents.user_name')
                        ->all();
                } elseif ($target === 'resellers' || $target === 'top_resellers') {
                    $limit = $count > 0 ? $count : 10;
                    $usernames = User::select('tbl_agents.user_name', DB::raw('SUM(tbl_transactions.amount) as total_amount'))
                        ->join('tbl_transactions', 'tbl_agents.user_name', '=', 'tbl_transactions.user_name')
                        ->where('tbl_agents.status', '=', 'reseller')
                        ->where('tbl_transactions.name', '!=', 'wallet funding')
                        ->groupBy('tbl_agents.user_name')
                        ->orderByDesc('total_amount')
                        ->limit($limit)
                        ->pluck('tbl_agents.user_name')
                        ->all();
                }

                if (empty($usernames)) {
                    throw new \RuntimeException('No eligible users found for this reward');
                }

                foreach ($usernames as $username) {
                    $code = $this->generateUniquePromoCode();
                    PromoCode::create([
                        'code' => $code,
                        'amount' => $amount,
                        'used' => 0,
                        'reuseable' => 0,
                        'usedby' => '',
                        'generated_for' => $username,
                    ]);
                }
            });
        } catch (\Throwable $e) {
            return redirect()->route('promo_codes.create')->with('error', $e->getMessage())->withInput();
        }

        return redirect()->route('promo_codes.index')->with('success', 'Reward processed successfully.');
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
        return redirect()->route('promo_codes.index')->with('error', 'Editing promo codes is not supported here.');
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
        return redirect()->route('promo_codes.index')->with('error', 'Updating promo codes is not supported here.');
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

    private function generateUniquePromoCode(): string
    {
        do {
            $code = 'PLF-' . strtoupper(Str::random(8));
        } while (PromoCode::where('code', $code)->exists());

        return $code;
    }

    private function upsertSetting(string $name, string $value): void
    {
        $setting = Settings::where('name', $name)->first();
        if ($setting) {
            $setting->value = $value;
            $setting->save();
            return;
        }

        Settings::create([
            'name' => $name,
            'value' => $value,
            'status' => 1,
        ]);
    }
}
