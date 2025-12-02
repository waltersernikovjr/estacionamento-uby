<?php

namespace App\Infrastructure\Persistence\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * ParkingSpot Model
 *
 * @property int $id
 * @property int|null $operator_id
 * @property string $number
 * @property string $type
 * @property float $hourly_price
 * @property float $width
 * @property float $length
 * @property string $status
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class ParkingSpot extends Model
{
    use HasFactory;

    protected $fillable = [
        'operator_id',
        'number',
        'type',
        'hourly_price',
        'width',
        'length',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'hourly_price' => 'decimal:2',
            'width' => 'decimal:2',
            'length' => 'decimal:2',
        ];
    }

    public function operator(): BelongsTo
    {
        return $this->belongsTo(Operator::class);
    }

    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class);
    }

    public function scopeAvailable($query)
    {
        return $query->where('status', 'available');
    }

    public function scopeOccupied($query)
    {
        return $query->where('status', 'occupied');
    }

    public function isAvailable(): bool
    {
        return $this->status === 'available';
    }

    public function getAreaAttribute(): float
    {
        return $this->width * $this->length;
    }
}
