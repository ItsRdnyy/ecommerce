<?php

namespace App\Listeners;

use App\Events\UserApproved;
use App\Models\User;
use App\Mail\AccountApprovedMail;
use App\Services\NotificationService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class HandleUserApproval
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
    public function handle(UserApproved $event): void
    {
        $user = $event->user;

        $code = sprintf("%06d", random_int(100000, 999999));
        $user->update([
            'status' => User::STATUS_APPROVED,
            'verification_code' => $code,
            'verification_expires_at' => now()->addMinutes(30),
        ]);

        if ($user->businessProfile) {
            $user->businessProfile->update(['verified_at' => now()]);
        }

        NotificationService::notify(
            $user,
            'account_approval',
            'Account Approved',
            "Your account has been approved by admin. Please enter this verification code to activate your account: {$code}",
            ['verification_code' => $code]
        );

        try {
            Mail::to($user->email)->send(new AccountApprovedMail($user, $code));
        } catch (\Exception $e) {
            Log::error("Failed to send approval email to {$user->email}: " . $e->getMessage());
        }

        Log::info("Verification code generated for {$user->email}: {$code}");
    }
}
