<?php

namespace App\Http\Controllers;

use App\Models\SsoToken;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SsoController extends Controller
{
    public function login(Request $request, $token)
    {
        $ssoToken = SsoToken::where('token', $token)
            ->where('expires_at', '>', now())
            ->first();

        if (!$ssoToken) {
            return redirect('/login')->withErrors(['email' => 'Invalid or expired SSO token.']);
        }

        // Find the primary user (e.g. owner) for this tenant
        // Since POS is multi-tenant by column, we look for an active owner of this tenant
        $user = User::where('tenant_id', $ssoToken->tenant_id)
            ->where('role', 'owner')
            ->where('is_active', true)
            ->first();

        // If no owner found, fallback to any active user of this tenant
        if (!$user) {
            $user = User::where('tenant_id', $ssoToken->tenant_id)
                ->where('is_active', true)
                ->first();
        }

        if (!$user) {
            return redirect('/login')->withErrors(['email' => 'No active users found for this tenant.']);
        }

        // Log the user in
        Auth::login($user);

        // Consume the token
        $ssoToken->delete();

        // Regenerate session to prevent fixation
        $request->session()->regenerate();

        return redirect()->intended(route('dashboard', absolute: false));
    }
}
