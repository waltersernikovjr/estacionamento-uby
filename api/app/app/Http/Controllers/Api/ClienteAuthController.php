<?php

namespace App\Http\Controllers\Api;

use App\Application\CreateCliente;
use App\Application\LoginCliente;
use App\Http\Controllers\Controller;
use App\Models\Cliente;
use Error;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

class ClienteAuthController extends Controller
{

    public function __construct(protected CreateCliente $create_cliente, protected LoginCliente $login_cliente) {}

    public function register(Request $request)
    {
        $output = $this->create_cliente->execute($request);

        $cliente = Cliente::where('id', $output->GetId())->first();

        $token = Auth::login($cliente);

        return response()->json([
            'message' => 'Cliente cadastrado com sucesso!',
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => config('jwt.ttl') * 60,
            'user' => $cliente
        ], 201);
    }

    public function login(Request $request)
    {
        $output = $this->login_cliente->execute($request);

        $cliente = Cliente::where('id', $output->GetId())->first();

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
            'user' => $cliente
        ]);
    }
}
