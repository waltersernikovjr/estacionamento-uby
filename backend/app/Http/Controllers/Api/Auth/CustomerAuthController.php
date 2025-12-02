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
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'phone' => ['nullable', 'string', 'max:20'],
            'zip_code' => ['required', 'string', 'max:9'],
            'street' => ['required', 'string', 'max:255'],
            'number' => ['required', 'string', 'max:20'],
            'complement' => ['nullable', 'string', 'max:255'],
            'neighborhood' => ['required', 'string', 'max:255'],
            'city' => ['required', 'string', 'max:255'],
            'state' => ['required', 'string', 'size:2'],
        ], [
            'name.required' => 'O nome é obrigatório',
            'name.max' => 'O nome deve ter no máximo 255 caracteres',
            'email.required' => 'O e-mail é obrigatório',
            'email.email' => 'Digite um e-mail válido',
            'email.unique' => 'Este e-mail já está cadastrado',
            'cpf.required' => 'O CPF é obrigatório',
            'cpf.size' => 'O CPF deve ter 11 dígitos',
            'cpf.unique' => 'Este CPF já está cadastrado',
            'password.required' => 'A senha é obrigatória',
            'password.min' => 'A senha deve ter no mínimo 8 caracteres',
            'password.confirmed' => 'As senhas não conferem',
            'phone.max' => 'O telefone deve ter no máximo 20 caracteres',
            'zip_code.required' => 'O CEP é obrigatório',
            'zip_code.max' => 'O CEP deve ter no máximo 9 caracteres',
            'street.required' => 'A rua é obrigatória',
            'street.max' => 'A rua deve ter no máximo 255 caracteres',
            'number.required' => 'O número é obrigatório',
            'number.max' => 'O número deve ter no máximo 20 caracteres',
            'complement.max' => 'O complemento deve ter no máximo 255 caracteres',
            'neighborhood.required' => 'O bairro é obrigatório',
            'neighborhood.max' => 'O bairro deve ter no máximo 255 caracteres',
            'city.required' => 'A cidade é obrigatória',
            'city.max' => 'A cidade deve ter no máximo 255 caracteres',
            'state.required' => 'O estado é obrigatório',
            'state.size' => 'O estado deve ter 2 caracteres (ex: SP)',
        ]);

        $customer = Customer::create([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'cpf' => $request->input('cpf'),
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

        Mail::to($customer->email)->send(new WelcomeCustomerMail($customer));

        return response()->json([
            'message' => 'Cadastro realizado com sucesso! Verifique seu email para ativar sua conta.',
            'email' => $customer->email,
            'requires_verification' => true,
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

        $customer = Customer::where('email', $request->input('email'))->first();

        if (!$customer || !Hash::check($request->input('password'), $customer->password)) {
            throw ValidationException::withMessages([
                'email' => ['As credenciais fornecidas estão incorretas.'],
            ]);
        }

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
