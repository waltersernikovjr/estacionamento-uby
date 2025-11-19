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
            'email' => ['required', 'string', 'email', 'max:255', 'unique:operators'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'phone' => ['nullable', 'string', 'max:20'],
        ]);

        $operator = Operator::create([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'password' => Hash::make($request->input('password')),
            'phone' => $request->input('phone'),
        ]);

        // Send welcome email with verification link
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
        ]);

        $operator = Operator::where('email', $request->input('email'))->first();

        if (!$operator || !Hash::check($request->input('password'), $operator->password)) {
            throw ValidationException::withMessages([
                'email' => ['As credenciais fornecidas estão incorretas.'],
            ]);
        }

        // Check if email is verified
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
