<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class EmailVerificationNotificationController extends Controller
{
    /**
     * Send a new email verification notification.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request)
    {
        if ($request->user()->hasVerifiedEmail()) {
            return $this->respondWithError('Your email is already verified.', ResponseAlias::HTTP_NOT_ACCEPTABLE);
        }

        $request->user()->sendEmailVerificationNotification();
        return $this->respond(['message' => 'A fresh verification link has been sent to your email address.'], ResponseAlias::HTTP_ACCEPTED);
    }
}
