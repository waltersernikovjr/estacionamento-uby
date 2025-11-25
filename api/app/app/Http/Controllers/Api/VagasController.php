<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cliente;
use App\Models\Vaga;
use App\Models\Veiculo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

class VagasController extends Controller
{
    public function index()
    {
        return Vaga::all();
    }

    public function store(Request $request)
    {
        dd($request->user());
        $request->validate([
            'numero' => 'required|unique:vagas',
            'preco_por_hora' => 'required|numeric|min:0',
            'largura' => 'required|numeric',
            'comprimento' => 'required|numeric',
        ]);

        $vaga = Vaga::create($request->all());

        return response()->json($vaga, 201);
    }
}
