<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

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

            if (!$user) {
                return $this->respondWithError('User not found', ResponseAlias::HTTP_NOT_FOUND);
            }

            if (!Hash::check($request->password, $user->password, [])) {
                return $this->respondWithError('Invalid credentials', ResponseAlias::HTTP_UNAUTHORIZED);
            }

            Auth::login($user);

            return $this->respond([
                'token' => $this->getToken($request, $user),
                'user' => $user,
            ], ResponseAlias::HTTP_ACCEPTED);
        } catch (\Exception $e) {
            return $this->respondWithError($e->getMessage());
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
            return $this->respondWithError($e->getMessage(), ResponseAlias::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function show()
    {
        return $this->respond([
            'data' => Auth::user()
        ]);
    }
}
