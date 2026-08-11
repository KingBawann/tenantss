<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SsoToken extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'sso_tokens';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'tenant_id',
        'token',
        'expires_at'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'expires_at' => 'datetime',
    ];
}
