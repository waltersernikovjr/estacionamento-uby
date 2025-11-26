<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;

class EstacionamentoRegistro extends Model
{
    use HasFactory;

    protected $table = 'estacionamento_registros'; // opcional, só por clareza

    protected $fillable = [
        'vaga_id',
        'veiculo_id',
        'entrada',
        'saida',
        'valor_total',
    ];

    protected $casts = [
        'entrada'     => 'datetime',
        'saida'       => 'datetime',
        'valor_total' => 'decimal:2',
    ];

    protected $appends = ['esta_estacionado', 'tempo_estacionado'];

    public function vaga()
    {
        return $this->belongsTo(Vaga::class);
    }

    public function veiculo()
    {
        return $this->belongsTo(Veiculo::class);
    }

    public function cliente()
    {
        return $this->hasOneThrough(
            Cliente::class,
            Veiculo::class,
            'id',
            'id',
            'veiculo_id',
            'cliente_id'
        );
    }

    public function getEstaEstacionadoAttribute(): bool
    {
        return is_null($this->saida);
    }
}
