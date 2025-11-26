<?php

namespace App\Repositories;

use App\Domain\Dimensao;
use App\Domain\Periodo;
use App\Domain\Vaga;
use App\Domain\Ticket;
use Illuminate\Support\Carbon;
use Exception;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Facades\DB;
use Nette\NotImplementedException;

class DatabaseVagaRepository implements VagaRepository
{

    public function Get(Criteria $criteria): ?Vaga
    {
        $vagaData = DB::table("vagas")->where($criteria->key, $criteria->value)->first();

        if (!$vagaData) return null;

        $currentTicket = DB::table('estacionamento_registros')
            ->where('vaga_id', $vagaData->id)
            ->whereNull('saida')
            ->whereNull('valor_total')
            ->first();

        $ticket = null;
        if ($currentTicket) {
            $ticket = new Ticket($currentTicket->veiculo_id, new Periodo(new Carbon($currentTicket->entrada), new Carbon($currentTicket->saida)), $currentTicket->valor_total, $currentTicket->id);
        }

        $vaga = new Vaga($vagaData->numero, $vagaData->preco_por_hora, new Dimensao($vagaData->largura, $vagaData->comprimento), $vagaData->disponivel, $ticket, $vagaData->id);

        return $vaga;
    }

    public function Save(Vaga $vaga): Vaga
    {
        if (!$vaga->GetId()) throw new NotImplementedException("Not implemented");

        DB::table("vagas")->where("id", $vaga->GetId())->update([
            "disponivel" => $vaga->GetDisponivel()
        ]);

        $ticket = $vaga->GetTicket();
        if ($ticket && $ticket->GetId()) {
            DB::table("estacionamento_registros")->where("id", $ticket->GetId())->update([
                "saida" => $ticket->GetFim(),
                "valor_total" => $ticket->GetPrecoTotal(),
            ]);
        } else if ($ticket) {
            $id = DB::table("estacionamento_registros")->insert([
                "vaga_id" => $vaga->GetId(),
                "veiculo_id" => $ticket->GetVeiculoId(),
                "entrada" => $ticket->GetComeco(),
            ]);

            $ticket->SetId($id);
        }

        return $vaga;
    }
}
