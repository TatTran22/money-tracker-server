<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;

class RegisteredUserController extends Controller
{
    /**
     * Handle an incoming registration request.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {
        try {
            $data = $request->validate([
                'first_name' => ['required', 'string', 'max:255'],
                'last_name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
                'password' => ['required', 'confirmed', Rules\Password::min(8)->letters()->mixedCase()->numbers()->symbols()->uncompromised()],
            ]);

            $nickname = Str::slug($data['first_name'] . ' ' . $data['last_name']);
            if (User::where('nickname', $nickname)->exists()) {
                $nickname .= '-' . Str::random(4);
            }

            $user = User::create([
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'nickname' => $nickname,
                'email' => $data['email'],
                'uuid' => Str::uuid(),
                'password' => Hash::make($data['password']),
            ]);

            event(new Registered($user));

            Auth::login($user);
            return $this->respond([
                'token' => $this->getToken($request, $user),
                'user' => $user,
            ], Response::HTTP_CREATED);
        } catch (ValidationException $e) {
            return $this->respond([
                'errors' => $e->errors(),
                'status_code' => Response::HTTP_UNPROCESSABLE_ENTITY,
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (\Exception $e) {
            return $this->respondWithError($e->getMessage(), 500);
        }
    }
}
