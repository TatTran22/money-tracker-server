<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Traits\ApiResponse;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;
use JetBrains\PhpStorm\ArrayShape;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests, ApiResponse;

    /**
     * @param Request $request
     * @param User $user
     * @return array
     */
    #[ArrayShape(['token' => "string", 'token_type' => "string", 'expires_in' => "float|int", 'expires_at' => "mixed"])] protected function getToken(Request $request, User $user): array
    {
        $tokenResult = $user->createToken('authToken');
        $tokenResult->accessToken->update([
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'expires_at' => Carbon::now()->addMinutes(Config::get('sanctum.expiration', 1400))
        ]);

        $user->tokens()->where('expires_at', '<', Carbon::now())->delete();
        return [
            'token' => $tokenResult->plainTextToken,
            'token_type' => 'Bearer',
            'expires_in' => Carbon::parse($tokenResult->accessToken->expires_at)->diffInSeconds(now()),
            'expires_at' => $tokenResult->accessToken->expires_at,
        ];
    }
}
