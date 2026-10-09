<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/** A mobile-app login (see the api_tokens migration). */
class ApiToken extends Model
{
    protected $fillable = ['user_id', 'name', 'token_hash', 'last_used_at'];

    protected $casts = ['last_used_at' => 'datetime'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /** Creates a token for this user and returns the plain text once (the app stores it). */
    public static function issue(User $user, ?string $name = null): string
    {
        $plain = 'rk_' . Str::random(60);
        static::create([
            'user_id' => $user->id,
            'name' => $name ? Str::limit($name, 100, '') : null,
            'token_hash' => hash('sha256', $plain),
        ]);

        return $plain;
    }

    public static function findByPlain(?string $plain): ?self
    {
        if (! $plain || ! str_starts_with($plain, 'rk_')) {
            return null;
        }

        return static::with('user')->where('token_hash', hash('sha256', $plain))->first();
    }
}
