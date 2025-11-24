<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vaga extends Model
{
    use HasFactory;

    protected $fillable = [
        'numero',
        'preco_por_hora',
        'largura',
        'comprimento',
        'disponivel',
        'tipo', // opcional: moto, carro, pcd, idoso, etc.
    ];

    protected $casts = [
        'preco_por_hora' => 'decimal:2',
        'largura'        => 'decimal:2',
        'comprimento'   => 'decimal:2',
        'disponivel'     => 'boolean',
    ];

    protected $appends = ['status'];

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

    public function veiculoEstacionado()
    {
        return $this->hasOneThrough(
            Veiculo::class,
            EstacionamentoRegistro::class,
            'vaga_id',
            'id',
            'id',
            'veiculo_id'
        )->whereNull('estacionamento_registros.saida');
    }

    public function getStatusAttribute(): string
    {
        return $this->disponivel ? 'disponível' : 'ocupada';
    }

    public function getOcupadaAttribute(): bool
    {
        return ! $this->disponivel;
    }

    public function scopeDisponiveis($query)
    {
        return $query->where('disponivel', true);
    }

    public function scopeOcupadas($query)
    {
        return $query->where('disponivel', false);
    }

    public function scopeDoTipo($query, $tipo)
    {
        return $query->where('tipo', $tipo);
    }

    public function ocupar(Veiculo $veiculo): EstacionamentoRegistro
    {
        if (! $this->disponivel) {
            throw new \Exception("Vaga {$this->numero} já está ocupada.");
        }

        $this->update(['disponivel' => false]);

        return $this->registros()->create([
            'veiculo_id' => $veiculo->id,
            'entrada'    => now(),
        ]);
    }

    public function liberar(): void
    {
        $this->update(['disponivel' => true]);
    }
}
