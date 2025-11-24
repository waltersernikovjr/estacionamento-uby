<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cliente;
use App\Models\Veiculo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

class ClienteAuthController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'nome' => 'required|string|max:255',
            'cpf' => 'required|string|size:11|unique:clientes,cpf',
            'rg' => 'required|string|unique:clientes,rg',
            'endereco' => 'required|string',

            'veiculo.placa' => 'required|string|unique:veiculos,placa',
            'veiculo.modelo' => 'required|string|max:100',
            'veiculo.cor' => 'required|string|max:30',
            'veiculo.ano' => 'required|integer|min:1900|max:' . (date('Y') + 1),

            'password' => 'required|string|min:6|confirmed',
        ]);

        $cliente = Cliente::create([
            'nome' => $request->nome,
            'cpf' => $request->cpf,
            'rg' => $request->rg,
            'endereco' => $request->endereco,
            'password' => bcrypt($request->password),
        ]);

        $cliente->veiculos()->create([
            'placa' => $request->input('veiculo.placa'),
            'modelo' => $request->input('veiculo.modelo'),
            'cor' => $request->input('veiculo.cor'),
            'ano' => $request->input('veiculo.ano'),
        ]);

        $token = Auth::login($cliente);

        return response()->json([
            'message' => 'Cliente cadastrado com sucesso!',
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => config('jwt.ttl') * 60,
            'cliente' => $cliente->load('veiculos')
        ], 201);
    }

    public function login(Request $request)
    {
        $request->validate([
            'cpf' => 'required|string|size:11',
            'password' => 'required|string',
        ]);

        $cliente = Cliente::where('cpf', $request->cpf)->first();

        if (!$cliente || !Hash::check($request->password, $cliente->password)) {
            return response()->json(['error' => 'CPF ou senha incorretos.'], 401);
        }

        $token = JWTAuth::fromUser($cliente);

        return $this->respondWithToken($token, $cliente);
    }

    public function logout()
    {
        Auth::logout();
        return response()->json(['message' => 'Logout realizado com sucesso']);
    }

    protected function respondWithToken($token, $cliente)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => config('jwt.ttl') * 60,
            'cliente' => $cliente
        ]);
    }
}
