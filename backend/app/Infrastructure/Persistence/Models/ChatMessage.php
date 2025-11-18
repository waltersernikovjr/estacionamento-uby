<?php

namespace App\Infrastructure\Persistence\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * ChatMessage Model
 *
 * @property int $id
 * @property int $chat_session_id
 * @property string $sender_type
 * @property int $sender_id
 * @property string $message
 * @property \Carbon\Carbon|null $read_at
 * @property \Carbon\Carbon $created_at
 */
class ChatMessage extends Model
{
    use HasFactory;

    const UPDATED_AT = null;

    protected $fillable = [
        'chat_session_id',
        'sender_type',
        'sender_id',
        'message',
        'read_at',
    ];

    protected function casts(): array
    {
        return [
            'read_at' => 'datetime',
        ];
    }

    public function chatSession(): BelongsTo
    {
        return $this->belongsTo(ChatSession::class);
    }

    public function sender()
    {
        return $this->sender_type === 'customer'
            ? Customer::find($this->sender_id)
            : Operator::find($this->sender_id);
    }

    public function markAsRead(): void
    {
        if (!$this->read_at) {
            $this->update(['read_at' => now()]);
        }
    }

    public function isRead(): bool
    {
        return $this->read_at !== null;
    }
}
