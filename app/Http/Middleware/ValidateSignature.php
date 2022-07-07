<?php

namespace App\Http\Middleware;

use App\Traits\ApiResponse;
use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Exceptions\InvalidSignatureException;

class ValidateSignature
{
    use ApiResponse;

    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param \Closure $next
     * @param string|null $relative
     * @return JsonResponse
     */
    public function handle($request, Closure $next, $relative = null)
    {
        try {
            if ($request->hasValidSignature($relative !== 'relative')) {
                return $next($request);
            }
            throw new InvalidSignatureException;
        } catch (InvalidSignatureException $e) {
            return $this->respondWithError('Invalid signature', 403);
        }
    }
}
