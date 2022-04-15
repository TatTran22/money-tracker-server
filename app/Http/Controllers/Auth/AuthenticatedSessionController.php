<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthenticatedSessionController extends Controller
{
    /**
     * Handle an incoming authentication request.
     *
     * @param \App\Http\Requests\Auth\LoginRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(LoginRequest $request)
    {
        try {
            $request->authenticate();
            /* @var User $user */
            $user = User::where('email', $request->email)->first();

            if (!Hash::check($request->password, $user->password, [])) {
                throw ValidationException::withMessages([
                    'password' => ['The provided password is invalid.'],
                ]);
            }
            $tokenResult = $user->createToken('authToken');
            $tokenResult->accessToken->update([
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'expires_at' => now()->addMinutes(config('sanctum.expiration')),
            ]);

            return $this->respond([
                'token' => $tokenResult->plainTextToken,
                'token_type' => 'Bearer',
                'expires_in' => Carbon::parse($tokenResult->accessToken->expires_at)->diffInSeconds(now()),
            ], 201);
        } catch (ValidationException $e) {
            return $this->respondWithError($e->getMessage(), $e->status);
        }
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy()
    {
        try {
            Auth::user()->currentAccessToken()->delete();

            return $this->respond([], 204);
        } catch (\Exception $e) {
            return $this->respondWithError($e->getMessage(), 500);
        }
    }
}
