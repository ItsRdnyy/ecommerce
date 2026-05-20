<?php

namespace App\Listeners;

use App\Events\UserRegistered;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class SendVerificationCode
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(UserRegistered $event): void
    {
        $user = $event->user;

        if ($user->status === User::STATUS_APPROVED) {
            $code = sprintf("%06d", random_int(100000, 999999));
            $user->update([
                'verification_code' => $code,
                'verification_expires_at' => now()->addMinutes(30),
            ]);

            try {
                Mail::to($user->email)->send(new \App\Mail\AccountApprovedMail($user, $code));
            } catch (\Exception $e) {
                Log::error("Failed to send verification email to {$user->email}: " . $e->getMessage());
            }
        }
    }
}
