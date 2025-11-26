<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Operador extends Authenticatable implements JWTSubject
{
    use HasFactory;

    protected $table = "operadores";
    protected $fillable = ['nome', 'cpf', 'email', 'password'];

    protected $hidden = ['password'];

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return ['role' => 'operador'];
    }

    public function vagas()
    {
        return $this->hasMany(Vaga::class);
    }
}
