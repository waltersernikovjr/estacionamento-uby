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

    public function getTempoEstacionadoAttribute()
    {
        if ($this->esta_estacionado) {
            $diff = $this->entrada->diffInMinutes(now());
        } else {
            $diff = $this->entrada->diffInMinutes($this->saida);
        }

        $hours   = floor($diff / 60);
        $minutes = $diff % 60;

        if ($hours > 0) {
            return "{$hours}h " . str_pad($minutes, 2, '0', STR_PAD_LEFT) . "min";
        }

        return "{$minutes}min";
    }

    public function registrarSaida(): self
    {
        if (!$this->esta_estacionado) {
            return $this;
        }

        $this->saida = now();
        $this->valor_total = $this->calcularValorTotal();
        $this->save();

        $this->vaga->update(['disponivel' => true]);

        return $this;
    }

    public function calcularValorTotal(): float
    {
        $minutos = $this->entrada->diffInMinutes($this->saida ?? now());

        if ($minutos <= 15) {
            return 0.00;
        }

        $minutosCobrado = $minutos - 15;

        $horasCobradas = ceil($minutosCobrado / 60);

        return $horasCobradas * $this->vaga->preco_por_hora;
    }

    public function scopeEstacionados($query)
    {
        return $query->whereNull('saida');
    }

    public function scopeDoDia($query, $date = null)
    {
        $date = $date ? Carbon::parse($date) : today();
        return $query->whereDate('entrada', $date);
    }

    public function scopeDoCliente($query, $clienteId)
    {
        return $query->whereHas('veiculo', function ($q) use ($clienteId) {
            $q->where('cliente_id', $clienteId);
        });
    }
}
