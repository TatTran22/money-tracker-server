<?php

namespace App\Http\Middleware;

use App\Models\User;
use App\Traits\ApiResponse;
use Closure;
use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class Authenticate extends Middleware
{
    use ApiResponse;

    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @param string[] ...$guards
     * @return mixed
     *
     * @throws \Illuminate\Auth\AuthenticationException
     */
    public function handle($request, Closure $next, ...$guards)
    {
        $this->authenticate($request, $guards);
        /* @var User $user */
        $user = $request->user();

        if ($request->ip() !== $user->currentAccessToken()->ip_address) {
            return $this->respondWithError('You are not authorized to access this resource.', 401);
        }
        $tokenExpireAt = $user->currentAccessToken()->expires_at;
        if (Carbon::now()->gt(Carbon::parse($tokenExpireAt))) {
            return $this->respondWithError('Your token has expired.', 401);
        }
        return $next($request);
    }
}
