<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Jobs\SendWelcomeAfterVerificationJob;
use Illuminate\Foundation\Auth\EmailVerificationRequest;

class VerificationController extends Controller
{
    public function verify(EmailVerificationRequest $request)
    {
        $user = $request->user();

        $wasVerified = $user->hasVerifiedEmail();

        $request->fulfill();

        if (!$wasVerified) {
            SendWelcomeAfterVerificationJob::dispatch($user->id);
        }

        return redirect('/products');
    }
}
