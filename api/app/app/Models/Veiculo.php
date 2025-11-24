<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Veiculo extends Model
{
    use HasFactory;

    protected $fillable = [
        'cliente_id',
        'placa',
        'modelo',
        'cor',
        'ano',
    ];

    protected $casts = [
        'ano' => 'integer',
    ];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function registros()
    {
        return $this->hasMany(EstacionamentoRegistro::class);
    }

    public function registroAtual()
    {
        return $this->hasOne(EstacionamentoRegistro::class)
            ->whereNull('saida')
            ->latest('entrada');
    }

    public function vagaAtual()
    {
        return $this->hasOneThrough(
            Vaga::class,
            EstacionamentoRegistro::class,
            'veiculo_id',
            'id',
            'id',
            'vaga_id'
        )->whereNull('estacionamento_registros.saida');
    }

    public function getPlacaFormatadaAttribute(): string
    {
        $placa = $this->placa;
        if (strlen($placa) === 7) {
            return substr($placa, 0, 3) . '-' . substr($placa, 3);
        }
        return $placa;
    }

    public function getEstaEstacionadoAttribute(): bool
    {
        return $this->registroAtual()->exists();
    }

    public function scopeEstacionados($query)
    {
        return $query->whereHas('registroAtual');
    }

    public function scopeDoCliente($query, $clienteId)
    {
        return $query->where('cliente_id', $clienteId);
    }
}
