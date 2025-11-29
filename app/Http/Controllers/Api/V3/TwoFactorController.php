<?php

namespace App\Http\Controllers\Api\V3;

use App\Http\Controllers\Controller;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use PragmaRX\Google2FA\Google2FA;

class TwoFactorController extends Controller
{
    protected $google2fa;

    public function __construct()
    {
        $this->google2fa = new Google2FA();
    }

    // Generate new secret and QR code (not yet enabled), return secret + qr as data URI
    public function setup(Request $request)
    {
        /** @var \App\Models\User */
        $user = $request->user();

        // Generate secret
        $secret = $this->google2fa->generateSecretKey();

        // Save encrypted secret temporarily (or prefer to store only after enable)
        // We'll store it but keep two_factor_enabled = false until user verifies code.
        $user->two_factor_secret = $secret;
        $user->two_factor_enabled = false;
        $user->save();

        // Create provisioning URI (used by Google Authenticator apps)
        $company = config('app.name');
        $label = $company . ':' . $user->email;
        $provisioningUri = $this->google2fa->getQRCodeUrl(
            $company,
            $user->email,
            $secret
        );

        // Create QR code as SVG data URI
        $renderer = new ImageRenderer(
            new RendererStyle(200),
            new SvgImageBackEnd()
        );
        $writer = new Writer($renderer);
        $svg = $writer->writeString($provisioningUri);
        $dataUri = 'data:image/svg+xml;base64,' . base64_encode($svg);

        return response()->json([
            'success' => 1,
            'secret' => $secret,          // you may omit sending secret to client for security
            'qr_code' => $dataUri,
            'otpauth_url' => $provisioningUri,
        ]);
    }

    // Verify code and enable 2FA
    public function toggle(Request $request)
    {
        $input = $request->all();
        $rules = array(
            'totp' => 'required',
        );

        $validator = Validator::make($input, $rules);

        $input = $request->all();

        if (!$validator->passes()) {
            return response()->json(['success' => 0, 'message' => implode(",", $validator->errors()->all())]);
        }

        /** @var \App\Models\User */
        $user = $request->user();

        if (!$user->two_factor_secret) {
            return response()->json(['success' => 0, 'message' => '2FA not setup yet. Setup first.'], 400);
        }

        $valid = $this->google2fa->verifyKey($user->two_factor_secret, $request->totp);

        if (!$valid) {
            return response()->json(['success' => 0, 'message' => 'Invalid TOTP code'], 403);
        }

        if ($user->two_factor_enabled) {
            $user->two_factor_enabled = false;
            $user->two_factor_secret = null;
            $user->two_factor_enabled_at = null;
            $user->save();

            return response()->json(['success' => 1, 'message' => 'Two-factor authentication disabled']);
        }

        $user->two_factor_enabled = true;
        $user->two_factor_enabled_at = now();
        $user->save();

        return response()->json(['success' => 1, 'message' => 'Two-factor authentication enabled']);
    }

    // Disable 2FA (you may require password or totp for extra safety)
    public function disable(Request $request)
    {
        $request->validate([
            'password' => 'required|string',
        ]);

        $user = $request->user();

        if (!\Hash::check($request->password, $user->password)) {
            return response()->json(['success' => false, 'message' => 'Invalid password'], 403);
        }

        $user->two_factor_enabled = false;
        $user->two_factor_secret = null;
        $user->two_factor_enabled_at = null;
        $user->save();

        return response()->json(['success' => true, 'message' => 'Two-factor authentication disabled']);
    }
}
