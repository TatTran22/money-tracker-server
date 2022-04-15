<?php

namespace App\Models\Sanctum;

class PersonalAccessToken extends \Laravel\Sanctum\PersonalAccessToken
{
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'abilities' => 'json',
        'last_used_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'ip_address',
        'user_agent',
        'token',
        'abilities',
        'expires_at',
    ];
}
