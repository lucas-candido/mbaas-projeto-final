<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject;
use App\Utils;

class Admin extends Authenticatable implements JWTSubject
{

    protected $connection = 'pgsql';

    protected $fillable = [
        'email', 'password', 'inativo', 'name'
    ];

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }
}
