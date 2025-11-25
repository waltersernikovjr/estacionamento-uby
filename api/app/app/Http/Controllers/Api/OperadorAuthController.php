<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Operador;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

class OperadorAuthController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'nome' => 'required|string|max:255',
            'cpf' => 'required|string|size:11|unique:operadores',
            'email' => 'required|email|unique:operadores',
            'password' => 'required|min:6|confirmed',
        ]);

        $operador = Operador::create([
            'nome' => $request->nome,
            'cpf' => $request->cpf,
            'email' => $request->email,
            'password' => $request->password,
        ]);

        $token = Auth::login($operador);

        return $this->respondWithToken($token, $operador);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $operador = Operador::where('email', $request->email)->first();

        if (!$operador || !Hash::check($request->password, $operador->password)) {
            return response()->json(['error' => 'Email ou senha incorretos.'], 401);
        }

        $token = JWTAuth::fromUser($operador);

        return response()->json([
            'access_token' => $token,
            'token_type'   => 'bearer',
            'expires_in'   => config('jwt.ttl') * 60,
            'operador'     => $operador,
        ]);
    }

    public function logout()
    {
        Auth::logout();
        return response()->json(['message' => 'Logout realizado com sucesso']);
    }

    protected function respondWithToken($token, $operador)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => config('jwt.ttl') * 60,
            'user' => $operador
        ]);
    }
}
