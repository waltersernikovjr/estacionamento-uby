<?php

namespace App\Http\Controllers;

/**
 * @OA\Info(
 *     version="1.0.0",
 *     title="Parking Management API - Uby",
 *     description="API completa para gerenciamento de estacionamento com autenticação, reservas, pagamentos e chat",
 *     @OA\Contact(
 *         email="contato@uby.com.br"
 *     ),
 *     @OA\License(
 *         name="Proprietary",
 *         url="https://uby.com.br"
 *     )
 * )
 * 
 * @OA\Server(
 *     url=L5_SWAGGER_CONST_HOST,
 *     description="API Server"
 * )
 * 
 * @OA\SecurityScheme(
 *     securityScheme="sanctum",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT",
 *     description="Token de autenticação via Laravel Sanctum"
 * )
 * 
 * @OA\Tag(
 *     name="Authentication",
 *     description="Endpoints de autenticação de clientes"
 * )
 * 
 * @OA\Tag(
 *     name="Customers",
 *     description="Gerenciamento de clientes"
 * )
 * 
 * @OA\Tag(
 *     name="Vehicles",
 *     description="Gerenciamento de veículos"
 * )
 * 
 * @OA\Tag(
 *     name="Parking Spots",
 *     description="Gerenciamento de vagas de estacionamento"
 * )
 * 
 * @OA\Tag(
 *     name="Reservations",
 *     description="Gerenciamento de reservas"
 * )
 * 
 * @OA\Tag(
 *     name="Payments",
 *     description="Gerenciamento de pagamentos"
 * )
 * 
 * @OA\Tag(
 *     name="Chat",
 *     description="Sistema de chat/suporte"
 * )
 * 
 * @OA\Tag(
 *     name="Utils",
 *     description="Utilitários (ViaCEP, etc)"
 * )
 */
abstract class Controller
{
}
