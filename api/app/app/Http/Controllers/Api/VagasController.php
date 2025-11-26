<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cliente;
use App\Models\Vaga;
use App\Models\Veiculo;
use Illuminate\Http\Request;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

class VagasController extends Controller
{
    public function index()
    {
        return Vaga::all();
    }

    public function store(Request $request)
    {
        $vaga = Vaga::create($request->all());

        return response()->json($vaga, 201);
    }

    public function ocupar($vagaId)
    {
        $user = JWTAuth::parseToken()->authenticate();

        $vaga = Vaga::findOrFail($vagaId);

        if (!$vaga->disponivel) {
            $vaga->liberar();

            return response()->json([
                "ocuped" => false
            ], 200);
        }

        $veiculo = Veiculo::where('cliente_id', $user->id)->first();

        if (!$veiculo) {
            return response()->json([
                'error' => 'Você não possui nenhum veículo cadastrado.'
            ], 400);
        }

        $vaga->ocupar($veiculo);

        return response()->json([
            "ocuped" => true
        ], 200);
    }
}
