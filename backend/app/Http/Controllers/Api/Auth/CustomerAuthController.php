<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Infrastructure\Mail\WelcomeCustomerMail;
use App\Infrastructure\Persistence\Models\Customer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;

class CustomerAuthController extends Controller
{
    public function register(Request $request): JsonResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:customers'],
            'cpf' => ['required', 'string', 'size:11', 'unique:customers'],
            'rg' => ['required', 'string', 'max:20'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'phone' => ['nullable', 'string', 'max:20'],
            'zip_code' => ['required', 'string', 'max:9'],
            'street' => ['required', 'string', 'max:255'],
            'number' => ['required', 'string', 'max:20'],
            'complement' => ['nullable', 'string', 'max:255'],
            'neighborhood' => ['required', 'string', 'max:255'],
            'city' => ['required', 'string', 'max:255'],
            'state' => ['required', 'string', 'size:2'],
        ]);

        $customer = Customer::create([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'cpf' => $request->input('cpf'),
            'rg' => $request->input('rg'),
            'password' => Hash::make($request->input('password')),
            'phone' => $request->input('phone'),
            'address_zipcode' => $request->input('zip_code'),
            'address_street' => $request->input('street'),
            'address_number' => $request->input('number'),
            'address_complement' => $request->input('complement'),
            'address_neighborhood' => $request->input('neighborhood'),
            'address_city' => $request->input('city'),
            'address_state' => $request->input('state'),
        ]);

        // Send welcome email with verification link
        Mail::to($customer->email)->send(new WelcomeCustomerMail($customer));

        $token = $customer->createToken('customer-token')->plainTextToken;

        return response()->json([
            'user' => [
                'id' => $customer->id,
                'name' => $customer->name,
                'email' => $customer->email,
                'cpf' => $customer->cpf,
                'phone' => $customer->phone,
                'type' => 'customer',
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

        $customer = Customer::where('email', $request->input('email'))->first();

        if (!$customer || !Hash::check($request->input('password'), $customer->password)) {
            throw ValidationException::withMessages([
                'email' => ['As credenciais fornecidas estão incorretas.'],
            ]);
        }

        // Check if email is verified
        if ($customer->email_verified_at === null) {
            throw ValidationException::withMessages([
                'email' => ['Por favor, verifique seu email antes de fazer login. Verifique sua caixa de entrada.'],
            ]);
        }

        $token = $customer->createToken('customer-token')->plainTextToken;

        return response()->json([
            'user' => [
                'id' => $customer->id,
                'name' => $customer->name,
                'email' => $customer->email,
                'cpf' => $customer->cpf,
                'phone' => $customer->phone,
                'type' => 'customer',
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
        $customer = $request->user();

        return response()->json([
            'data' => [
                'id' => $customer->id,
                'name' => $customer->name,
                'email' => $customer->email,
                'cpf' => $customer->cpf,
                'rg' => $customer->rg,
                'phone' => $customer->phone,
                'type' => 'customer',
                'address' => [
                    'zip_code' => $customer->address_zipcode,
                    'street' => $customer->address_street,
                    'number' => $customer->address_number,
                    'complement' => $customer->address_complement,
                    'neighborhood' => $customer->address_neighborhood,
                    'city' => $customer->address_city,
                    'state' => $customer->address_state,
                ],
            ],
        ]);
    }
}
