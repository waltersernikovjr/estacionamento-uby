<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Vaga;
use Illuminate\Http\Request;

class VagaController extends Controller
{
    public function __construct()
    {
        $this->method_exists('auth:api_operador');
    }

    public function index()
    {
        return Vaga::all();
    }

    public function store(Request $request)
    {
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
