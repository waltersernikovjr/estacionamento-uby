<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Infrastructure\Mail\WelcomeOperatorMail;
use App\Infrastructure\Persistence\Models\Operator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;

class OperatorAuthController extends Controller
{
    public function register(Request $request): JsonResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'cpf' => ['required', 'string', 'size:11', 'unique:operators'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:operators'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'phone' => ['nullable', 'string', 'max:20'],
        ], [
            'name.required' => 'O nome é obrigatório',
            'name.max' => 'O nome deve ter no máximo 255 caracteres',
            'cpf.required' => 'O CPF é obrigatório',
            'cpf.size' => 'O CPF deve ter 11 dígitos',
            'cpf.unique' => 'Este CPF já está cadastrado',
            'email.required' => 'O e-mail é obrigatório',
            'email.email' => 'Digite um e-mail válido',
            'email.unique' => 'Este e-mail já está cadastrado',
            'password.required' => 'A senha é obrigatória',
            'password.min' => 'A senha deve ter no mínimo 8 caracteres',
            'password.confirmed' => 'As senhas não conferem',
            'phone.max' => 'O telefone deve ter no máximo 20 caracteres',
        ]);

        $operator = Operator::create([
            'name' => $request->input('name'),
            'cpf' => $request->input('cpf'),
            'email' => $request->input('email'),
            'password' => Hash::make($request->input('password')),
            'phone' => $request->input('phone'),
        ]);

        Mail::to($operator->email)->send(new WelcomeOperatorMail($operator));

        $token = $operator->createToken('operator-token')->plainTextToken;

        return response()->json([
            'user' => [
                'id' => $operator->id,
                'name' => $operator->name,
                'email' => $operator->email,
                'phone' => $operator->phone,
                'type' => 'operator',
            ],
            'token' => $token,
        ], 201);
    }

    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ], [
            'email.required' => 'O e-mail é obrigatório',
            'email.email' => 'Digite um e-mail válido',
            'password.required' => 'A senha é obrigatória',
        ]);

        $operator = Operator::where('email', $request->input('email'))->first();

        if (!$operator || !Hash::check($request->input('password'), $operator->password)) {
            throw ValidationException::withMessages([
                'email' => ['As credenciais fornecidas estão incorretas.'],
            ]);
        }

        if ($operator->email_verified_at === null) {
            throw ValidationException::withMessages([
                'email' => ['Por favor, verifique seu email antes de fazer login. Verifique sua caixa de entrada.'],
            ]);
        }

        $token = $operator->createToken('operator-token')->plainTextToken;

        return response()->json([
            'user' => [
                'id' => $operator->id,
                'name' => $operator->name,
                'email' => $operator->email,
                'phone' => $operator->phone,
                'type' => 'operator',
            ],
            'token' => $token,
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logout realizado com sucesso',
        ]);
    }

    public function me(Request $request): JsonResponse
    {
        $operator = $request->user();

        return response()->json([
            'user' => [
                'id' => $operator->id,
                'name' => $operator->name,
                'email' => $operator->email,
                'phone' => $operator->phone,
                'type' => 'operator',
            ],
        ]);
    }
}
