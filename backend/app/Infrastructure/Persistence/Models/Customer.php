<?php

namespace App\Infrastructure\Persistence\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

/**
 * Customer Model
 *
 * @property int $id
 * @property string $name
 * @property string $cpf
 * @property string $rg
 * @property string $email
 * @property string $password
 * @property string $address_zipcode
 * @property string $address_street
 * @property string $address_number
 * @property string|null $address_complement
 * @property string $address_neighborhood
 * @property string $address_city
 * @property string $address_state
 * @property \Carbon\Carbon|null $email_verified_at
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class Customer extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'cpf',
        'rg',
        'email',
        'password',
        'phone',
        'email_verified_at',
        'address_zipcode',
        'address_street',
        'address_number',
        'address_complement',
        'address_neighborhood',
        'address_city',
        'address_state',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get the vehicles owned by this customer.
     */
    public function vehicles(): HasMany
    {
        return $this->hasMany(Vehicle::class);
    }

    /**
     * Get the reservations made by this customer.
     */
    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class);
    }

    /**
     * Get the chat sessions initiated by this customer.
     */
    public function chatSessions(): HasMany
    {
        return $this->hasMany(ChatSession::class);
    }

    /**
     * Get the full address as a string.
     */
    public function getFullAddressAttribute(): string
    {
        $complement = $this->address_complement ? ", {$this->address_complement}" : '';

        return "{$this->address_street}, {$this->address_number}{$complement}, "
            . "{$this->address_neighborhood}, {$this->address_city} - {$this->address_state}, "
            . "CEP: {$this->address_zipcode}";
    }
}
