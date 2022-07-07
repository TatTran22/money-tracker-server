<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class AuthenticatedSessionController extends Controller
{
    /**
     * Handle an incoming authentication request.
     *
     * @param LoginRequest $request
     * @return JsonResponse
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
                return $this->respondWithError(__('auth.failed'), ResponseAlias::HTTP_UNAUTHORIZED);
            }

            Auth::login($user, $request->input('remember', false));

            return $this->respond([
                'token' => $this->getToken($request, $user),
                'user' => $user,
            ], ResponseAlias::HTTP_ACCEPTED);
        } catch (\Exception $e) {
            return $this->respondWithError($e->getMessage());
        }
    }

    /**
     * @return JsonResponse
     */
    public function destroy(): JsonResponse
    {
        try {
            Auth::user()->currentAccessToken()->delete();

            return $this->respond([], 204);
        } catch (\Exception $e) {
            return $this->respondWithError($e->getMessage(), ResponseAlias::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @return JsonResponse
     */
    public function show(): JsonResponse
    {
        return $this->respond([
            'user' => Auth::user()
        ]);
    }
}
