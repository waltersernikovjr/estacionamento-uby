<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Infrastructure\Persistence\Models\Reservation;
use Carbon\Carbon;
use Illuminate\Console\Command;

class AdjustReservationEntryTime extends Command
{
    protected $signature = 'reservation:adjust-time {reservationId} {--hours=2 : Horas para subtrair da entrada}';

    protected $description = 'Ajusta o horário de entrada de uma reserva para simular tempo decorrido';

    public function handle(): int
    {
        $reservationId = (int) $this->argument('reservationId');
        $hoursToSubtract = (int) $this->option('hours');

        $reservation = Reservation::find($reservationId);

        if (!$reservation) {
            $this->error("Reserva #{$reservationId} não encontrada!");
            return self::FAILURE;
        }

        if ($reservation->status !== 'active') {
            $this->error("Apenas reservas ativas podem ter o horário ajustado!");
            return self::FAILURE;
        }

        $oldEntryTime = $reservation->entry_time;
        $newEntryTime = Carbon::parse($reservation->entry_time)->subHours($hoursToSubtract);

        $reservation->entry_time = $newEntryTime;
        $reservation->save();

        $this->info("✅ Reserva #{$reservationId} ajustada com sucesso!");
        $this->line("📅 Entrada anterior: {$oldEntryTime->format('d/m/Y H:i:s')}");
        $this->line("📅 Nova entrada: {$newEntryTime->format('d/m/Y H:i:s')}");
        $this->line("⏱️  Tempo decorrido simulado: {$hoursToSubtract} horas");

        $now = Carbon::now();
        $hours = $newEntryTime->diffInHours($now);
        $minutes = $newEntryTime->copy()->addHours($hours)->diffInMinutes($now);
        $parkingSpot = $reservation->parkingSpot;
        $hourlyPrice = $parkingSpot ? (float) $parkingSpot->hourly_price : 5.0;

        $estimatedCost = $hours * $hourlyPrice;
        if ($minutes > 0) {
            $estimatedCost += ($minutes / 60) * $hourlyPrice;
        }

        $this->line("💰 Valor estimado ao fazer checkout agora: R$ " . number_format($estimatedCost, 2, ',', '.'));

        return self::SUCCESS;
    }
}
