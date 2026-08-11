<?php

namespace App\Listeners;

use App\Models\LoginLog;
use Illuminate\Auth\Events\Login;
use Illuminate\Http\Request;

class LogSuccessfulLogin
{
    /**
     * Create the event listener.
     */
    public function __construct(
        protected Request $request,
    ) {}

    /**
     * Handle the event.
     */
    public function handle(Login $event): void
    {
        $user = $event->user;

        // Only log if user has a tenant_id (skip platform-level users if any)
        if ($user->tenant_id) {
            LoginLog::create([
                'tenant_id'    => $user->tenant_id,
                'user_id'      => $user->id,
                'ip_address'   => $this->request->ip(),
                'user_agent'   => $this->request->userAgent(),
                'status'       => 'success',
                'logged_in_at' => now(),
            ]);
        }
    }
}
