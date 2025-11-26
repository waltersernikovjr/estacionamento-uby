<?php

namespace App\Http\Controllers\Api;

use App\Application\UpdateVaga;
use App\Http\Controllers\Controller;
use App\Models\Cliente;
use App\Models\Vaga;
use App\Models\Veiculo;
use Illuminate\Http\Request;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

class VagasController extends Controller
{
    public function __construct(protected UpdateVaga $update_vaga) {}

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
        $this->update_vaga->execute($vagaId);

        return response()->json([
            "ocuped" => true
        ], 200);
    }
}
