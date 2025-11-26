<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Cliente extends Authenticatable implements JWTSubject
{
    use HasFactory;

    protected $fillable = ['nome', 'cpf', 'email', 'rg', 'endereco', 'password'];

    public function veiculos()
    {
        return $this->hasMany(Veiculo::class);
    }

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }
    public function getJWTCustomClaims()
    {
        return ['role' => 'cliente'];
    }
}
