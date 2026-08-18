<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class PinController extends Controller
{
    public function verify(Request $request)
    {
        $request->validate([
            'pin' => 'required|string',
        ]);

        $pin = $request->input('pin');

        // Find any manager or admin with a matching PIN
        // Note: For security, we should ideally identify the specific manager,
        // but for a generic PIN check, we can check all managers' pins.
        $managers = User::whereIn('role', ['admin', 'manager', 'owner'])
            ->whereNotNull('manager_pin')
            ->get();

        foreach ($managers as $manager) {
            if (Hash::check($pin, $manager->manager_pin)) {
                return response()->json(['authorized' => true, 'manager' => $manager->name]);
            }
        }

        return response()->json(['authorized' => false], 403);
    }
}
