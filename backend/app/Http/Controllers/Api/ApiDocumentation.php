<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

/**
 * Arquivo dedicado exclusivamente para documentação Swagger/OpenAPI
 * NÃO adicionar lógica de negócio aqui
 */

/**
 * @OA\Post(
 *     path="/api/v1/customers/register",
 *     summary="Registrar novo cliente",
 *     description="Cria uma nova conta de cliente e envia email de verificação. O cliente precisa verificar o email antes de fazer login.",
 *     operationId="registerCustomer",
 *     tags={"Authentication"},
 *     @OA\RequestBody(
 *         required=true,
 *         description="Dados do cliente",
 *         @OA\JsonContent(
 *             required={"name","email","cpf","password","password_confirmation","zip_code","street","number","neighborhood","city","state"},
 *             @OA\Property(property="name", type="string", example="João Silva", description="Nome completo"),
 *             @OA\Property(property="email", type="string", format="email", example="joao.silva@email.com", description="Email único"),
 *             @OA\Property(property="cpf", type="string", example="12345678900", description="CPF com 11 dígitos (sem pontos)"),
 *             @OA\Property(property="password", type="string", format="password", example="password123", description="Senha (mínimo 8 caracteres)"),
 *             @OA\Property(property="password_confirmation", type="string", format="password", example="password123", description="Confirmação da senha"),
 *             @OA\Property(property="phone", type="string", example="11999999999", description="Telefone (opcional)"),
 *             @OA\Property(property="zip_code", type="string", example="01310100", description="CEP com 8 dígitos"),
 *             @OA\Property(property="street", type="string", example="Av Paulista", description="Logradouro"),
 *             @OA\Property(property="number", type="string", example="1000", description="Número"),
 *             @OA\Property(property="complement", type="string", example="Apto 101", description="Complemento (opcional)"),
 *             @OA\Property(property="neighborhood", type="string", example="Bela Vista", description="Bairro"),
 *             @OA\Property(property="city", type="string", example="São Paulo", description="Cidade"),
 *             @OA\Property(property="state", type="string", example="SP", description="Estado (2 letras)")
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="Cliente registrado com sucesso, email de verificação enviado",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Cadastro realizado com sucesso! Verifique seu email para ativar sua conta."),
 *             @OA\Property(property="email", type="string", example="joao.silva@email.com"),
 *             @OA\Property(property="requires_verification", type="boolean", example=true)
 *         )
 *     ),
 *     @OA\Response(response=422, description="Dados inválidos")
 * )
 *
 * @OA\Post(
 *     path="/api/v1/customers/login",
 *     summary="Login de cliente",
 *     description="Autentica um cliente e retorna token de acesso. O email precisa estar verificado.",
 *     operationId="loginCustomer",
 *     tags={"Authentication"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"email","password"},
 *             @OA\Property(property="email", type="string", format="email", example="joao.silva@email.com"),
 *             @OA\Property(property="password", type="string", format="password", example="password123")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Login realizado com sucesso",
 *         @OA\JsonContent(
 *             @OA\Property(property="token", type="string", example="1|abc123..."),
 *             @OA\Property(
 *                 property="user",
 *                 type="object",
 *                 @OA\Property(property="id", type="integer", example=1),
 *                 @OA\Property(property="name", type="string", example="João Silva"),
 *                 @OA\Property(property="email", type="string", example="joao.silva@email.com"),
 *                 @OA\Property(property="cpf", type="string", example="12345678900"),
 *                 @OA\Property(property="phone", type="string", example="11999999999"),
 *                 @OA\Property(property="type", type="string", example="customer")
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Credenciais inválidas ou email não verificado",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Por favor, verifique seu email antes de fazer login."),
 *             @OA\Property(
 *                 property="errors",
 *                 type="object",
 *                 @OA\Property(property="email", type="array", @OA\Items(type="string"))
 *             )
 *         )
 *     )
 * )
 *
 * @OA\Post(
 *     path="/api/v1/customers/logout",
 *     summary="Logout de cliente",
 *     description="Revoga o token de acesso atual do cliente",
 *     operationId="logoutCustomer",
 *     tags={"Authentication"},
 *     security={{"sanctum":{}}},
 *     @OA\Response(
 *         response=200,
 *         description="Logout realizado com sucesso",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Logout realizado com sucesso")
 *         )
 *     ),
 *     @OA\Response(response=401, description="Não autenticado")
 * )
 *
 * @OA\Post(
 *     path="/api/v1/email/resend",
 *     summary="Reenviar email de verificação",
 *     description="Reenvia o email de verificação para clientes ou operadores",
 *     operationId="resendVerificationEmail",
 *     tags={"Authentication"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"email","type"},
 *             @OA\Property(property="email", type="string", format="email", example="joao.silva@email.com"),
 *             @OA\Property(property="type", type="string", enum={"customer","operator"}, example="customer")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Email reenviado com sucesso",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Email de verificação reenviado com sucesso.")
 *         )
 *     ),
 *     @OA\Response(response=400, description="Email já está verificado"),
 *     @OA\Response(response=404, description="Usuário não encontrado")
 * )
 *
 * @OA\Get(
 *     path="/api/v1/customers/me",
 *     summary="Obter dados do cliente autenticado",
 *     description="Retorna os dados completos do cliente autenticado incluindo endereço",
 *     operationId="getAuthenticatedCustomer",
 *     tags={"Authentication"},
 *     security={{"sanctum":{}}},
 *     @OA\Response(
 *         response=200,
 *         description="Dados do cliente",
 *         @OA\JsonContent(
 *             @OA\Property(property="id", type="integer", example=1),
 *             @OA\Property(property="name", type="string", example="João Silva"),
 *             @OA\Property(property="email", type="string", example="joao.silva@email.com"),
 *             @OA\Property(property="cpf", type="string", example="12345678900"),
 *             @OA\Property(property="phone", type="string", example="11999999999"),
 *             @OA\Property(property="email_verified_at", type="string", format="date-time", example="2024-01-15T10:30:00.000000Z"),
 *             @OA\Property(
 *                 property="address",
 *                 type="object",
 *                 @OA\Property(property="zip_code", type="string", example="01310100"),
 *                 @OA\Property(property="street", type="string", example="Av Paulista"),
 *                 @OA\Property(property="number", type="string", example="1000"),
 *                 @OA\Property(property="complement", type="string", nullable=true),
 *                 @OA\Property(property="neighborhood", type="string", example="Bela Vista"),
 *                 @OA\Property(property="city", type="string", example="São Paulo"),
 *                 @OA\Property(property="state", type="string", example="SP")
 *             )
 *         )
 *     ),
 *     @OA\Response(response=401, description="Não autenticado")
 * )
 *
 * @OA\Post(
 *     path="/api/v1/vehicles",
 *     summary="Criar novo veículo",
 *     description="Registra um novo veículo para o cliente autenticado",
 *     operationId="createVehicle",
 *     tags={"Vehicles"},
 *     security={{"sanctum":{}}},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"customer_id","license_plate","brand","model","color","type"},
 *             @OA\Property(property="customer_id", type="integer", example=1),
 *             @OA\Property(property="license_plate", type="string", example="ABC1D234", description="Placa no formato Mercosul"),
 *             @OA\Property(property="brand", type="string", example="Toyota", description="Marca do veículo"),
 *             @OA\Property(property="model", type="string", example="Corolla"),
 *             @OA\Property(property="color", type="string", example="Prata"),
 *             @OA\Property(property="type", type="string", enum={"car","motorcycle","truck"}, example="car")
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="Veículo criado com sucesso",
 *         @OA\JsonContent(
 *             @OA\Property(
 *                 property="data",
 *                 type="object",
 *                 @OA\Property(property="id", type="integer", example=1),
 *                 @OA\Property(property="customer_id", type="integer", example=1),
 *                 @OA\Property(property="license_plate", type="string", example="ABC1D234"),
 *                 @OA\Property(property="brand", type="string", example="Toyota"),
 *                 @OA\Property(property="model", type="string", example="Corolla"),
 *                 @OA\Property(property="color", type="string", example="Prata"),
 *                 @OA\Property(property="type", type="string", example="car"),
 *                 @OA\Property(property="created_at", type="string", format="date-time"),
 *                 @OA\Property(property="updated_at", type="string", format="date-time")
 *             )
 *         )
 *     ),
 *     @OA\Response(response=401, description="Não autenticado"),
 *     @OA\Response(response=422, description="Dados inválidos")
 * )
 *
 * @OA\Get(
 *     path="/api/v1/vehicles",
 *     summary="Listar veículos do cliente",
 *     description="Retorna todos os veículos do cliente autenticado",
 *     operationId="listVehicles",
 *     tags={"Vehicles"},
 *     security={{"sanctum":{}}},
 *     @OA\Response(
 *         response=200,
 *         description="Lista de veículos",
 *         @OA\JsonContent(
 *             @OA\Property(property="data", type="array", @OA\Items(type="object"))
 *         )
 *     )
 * )
 *
 * @OA\Post(
 *     path="/api/v1/parking-spots",
 *     summary="Criar nova vaga",
 *     description="Cria uma nova vaga de estacionamento. Valores padrão: hourly_price=5.00, width=2.50, length=5.00",
 *     operationId="createParkingSpot",
 *     tags={"Parking Spots"},
 *     security={{"sanctum":{}}},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"number","type"},
 *             @OA\Property(property="number", type="string", example="A01", description="Número/identificação da vaga"),
 *             @OA\Property(property="type", type="string", enum={"regular","vip","disabled"}, example="regular"),
 *             @OA\Property(property="hourly_price", type="number", format="float", example=5.00, description="Valor por hora (padrão: 5.00)"),
 *             @OA\Property(property="width", type="number", format="float", example=2.50, description="Largura em metros (padrão: 2.50)"),
 *             @OA\Property(property="length", type="number", format="float", example=5.00, description="Comprimento em metros (padrão: 5.00)"),
 *             @OA\Property(property="operator_id", type="integer", example=1, nullable=true, description="ID do operador (opcional)")
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="Vaga criada com sucesso",
 *         @OA\JsonContent(
 *             @OA\Property(
 *                 property="data",
 *                 type="object",
 *                 @OA\Property(property="id", type="integer", example=1),
 *                 @OA\Property(property="number", type="string", example="A01"),
 *                 @OA\Property(property="type", type="string", example="regular"),
 *                 @OA\Property(property="status", type="string", example="available"),
 *                 @OA\Property(property="created_at", type="string", format="date-time"),
 *                 @OA\Property(property="updated_at", type="string", format="date-time")
 *             )
 *         )
 *     )
 * )
 *
 * @OA\Get(
 *     path="/api/v1/parking-spots",
 *     summary="Listar vagas",
 *     description="Lista vagas de estacionamento. Pode filtrar por status (available, occupied, reserved)",
 *     operationId="listParkingSpots",
 *     tags={"Parking Spots"},
 *     security={{"sanctum":{}}},
 *     @OA\Parameter(
 *         name="status",
 *         in="query",
 *         description="Filtrar por status",
 *         required=false,
 *         @OA\Schema(type="string", enum={"available","occupied","reserved"})
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Lista de vagas",
 *         @OA\JsonContent(
 *             @OA\Property(property="data", type="array", @OA\Items(type="object"))
 *         )
 *     )
 * )
 *
 * @OA\Post(
 *     path="/api/v1/reservations",
 *     summary="Criar reserva",
 *     description="Cria uma nova reserva de vaga. A vaga fica com status occupied e total_amount fica null até completar",
 *     operationId="createReservation",
 *     tags={"Reservations"},
 *     security={{"sanctum":{}}},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"customer_id","vehicle_id","parking_spot_id","entry_time"},
 *             @OA\Property(property="customer_id", type="integer", example=1),
 *             @OA\Property(property="vehicle_id", type="integer", example=1),
 *             @OA\Property(property="parking_spot_id", type="integer", example=1),
 *             @OA\Property(property="entry_time", type="string", format="date-time", example="2025-11-19T12:00:00", description="Data/hora de entrada no formato ISO 8601")
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="Reserva criada",
 *         @OA\JsonContent(
 *             @OA\Property(
 *                 property="data",
 *                 type="object",
 *                 @OA\Property(property="id", type="integer", example=1),
 *                 @OA\Property(property="vehicle_id", type="integer", example=1),
 *                 @OA\Property(property="entry_time", type="string", format="date-time"),
 *                 @OA\Property(property="exit_time", type="string", format="date-time", nullable=true),
 *                 @OA\Property(property="expected_exit_time", type="string", format="date-time", nullable=true),
 *                 @OA\Property(property="total_amount", type="string", nullable=true, description="Valor total (null até completar)"),
 *                 @OA\Property(property="status", type="string", example="active"),
 *                 @OA\Property(property="created_at", type="string", format="date-time"),
 *                 @OA\Property(property="updated_at", type="string", format="date-time")
 *             )
 *         )
 *     )
 * )
 *
 * @OA\Post(
 *     path="/api/v1/reservations/{id}/complete",
 *     summary="Completar reserva",
 *     description="Finaliza a reserva calculando o valor total. Cálculo: R$5/hora + R$1 por bloco de 15min extras. A vaga volta para status available",
 *     operationId="completeReservation",
 *     tags={"Reservations"},
 *     security={{"sanctum":{}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         description="ID da reserva",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"exit_time"},
 *             @OA\Property(property="exit_time", type="string", format="date-time", example="2025-11-19T14:00:00", description="Data/hora de saída")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Reserva completada com sucesso",
 *         @OA\JsonContent(
 *             @OA\Property(
 *                 property="data",
 *                 type="object",
 *                 @OA\Property(property="id", type="integer", example=1),
 *                 @OA\Property(property="vehicle_id", type="integer", example=1),
 *                 @OA\Property(property="entry_time", type="string", format="date-time"),
 *                 @OA\Property(property="exit_time", type="string", format="date-time"),
 *                 @OA\Property(property="total_amount", type="string", example="10.00", description="Valor total calculado"),
 *                 @OA\Property(property="status", type="string", example="completed"),
 *                 @OA\Property(property="created_at", type="string", format="date-time"),
 *                 @OA\Property(property="updated_at", type="string", format="date-time")
 *             )
 *         )
 *     )
 * )
 *
 * @OA\Post(
 *     path="/api/v1/payments",
 *     summary="Criar pagamento",
 *     description="Cria um pagamento para uma reserva completada",
 *     operationId="createPayment",
 *     tags={"Payments"},
 *     security={{"sanctum":{}}},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"reservation_id","amount","payment_method"},
 *             @OA\Property(property="reservation_id", type="integer", example=1),
 *             @OA\Property(property="amount", type="number", format="float", example=10.00, description="Valor do pagamento"),
 *             @OA\Property(property="payment_method", type="string", enum={"credit_card","debit_card","pix","cash","others"}, example="credit_card"),
 *             @OA\Property(property="status", type="string", enum={"pending","paid","cancelled"}, example="pending")
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="Pagamento criado",
 *         @OA\JsonContent(
 *             @OA\Property(
 *                 property="data",
 *                 type="object",
 *                 @OA\Property(property="id", type="integer", example=1),
 *                 @OA\Property(property="amount", type="string", example="10.00"),
 *                 @OA\Property(property="payment_method", type="string", example="credit_card"),
 *                 @OA\Property(property="status", type="string", example="pending"),
 *                 @OA\Property(property="paid_at", type="string", format="date-time", nullable=true),
 *                 @OA\Property(property="created_at", type="string", format="date-time"),
 *                 @OA\Property(property="updated_at", type="string", format="date-time")
 *             )
 *         )
 *     )
 * )
 *
 * @OA\Post(
 *     path="/api/v1/payments/{id}/mark-as-paid",
 *     summary="Marcar pagamento como pago",
 *     description="Atualiza o status do pagamento para paid e registra a data/hora do pagamento",
 *     operationId="markPaymentAsPaid",
 *     tags={"Payments"},
 *     security={{"sanctum":{}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         description="ID do pagamento",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Pagamento confirmado",
 *         @OA\JsonContent(
 *             @OA\Property(
 *                 property="data",
 *                 type="object",
 *                 @OA\Property(property="id", type="integer", example=1),
 *                 @OA\Property(property="amount", type="string", example="10.00"),
 *                 @OA\Property(property="payment_method", type="string", example="credit_card"),
 *                 @OA\Property(property="status", type="string", example="paid"),
 *                 @OA\Property(property="paid_at", type="string", format="date-time"),
 *                 @OA\Property(property="created_at", type="string", format="date-time"),
 *                 @OA\Property(property="updated_at", type="string", format="date-time")
 *             )
 *         )
 *     )
 * )
 *
 * @OA\Get(
 *     path="/api/v1/viacep/{zipcode}",
 *     summary="Consultar CEP via ViaCEP",
 *     description="Consulta dados de endereço através do CEP utilizando a API ViaCEP",
 *     operationId="getAddressByZipCode",
 *     tags={"Utils"},
 *     @OA\Parameter(
 *         name="zipcode",
 *         in="path",
 *         description="CEP com 8 dígitos (sem pontos ou hífen)",
 *         required=true,
 *         @OA\Schema(type="string", example="01310100")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Dados do endereço",
 *         @OA\JsonContent(
 *             @OA\Property(
 *                 property="data",
 *                 type="object",
 *                 @OA\Property(property="zip_code", type="string", example="01310-100"),
 *                 @OA\Property(property="street", type="string", example="Avenida Paulista"),
 *                 @OA\Property(property="complement", type="string", example="de 612 a 1510 - lado par"),
 *                 @OA\Property(property="neighborhood", type="string", example="Bela Vista"),
 *                 @OA\Property(property="city", type="string", example="São Paulo"),
 *                 @OA\Property(property="state", type="string", example="SP"),
 *                 @OA\Property(property="ibge", type="string", example="3550308")
 *             )
 *         )
 *     ),
 *     @OA\Response(response=404, description="CEP não encontrado")
 * )
 *
 * @OA\Post(
 *     path="/api/v1/operators/register",
 *     summary="Registrar novo operador",
 *     description="Cria uma nova conta de operador e envia email de verificação",
 *     operationId="registerOperator",
 *     tags={"Operators"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"name","email","password","password_confirmation"},
 *             @OA\Property(property="name", type="string", example="Maria Operadora"),
 *             @OA\Property(property="email", type="string", format="email", example="maria@email.com"),
 *             @OA\Property(property="password", type="string", format="password", example="password123"),
 *             @OA\Property(property="password_confirmation", type="string", format="password", example="password123"),
 *             @OA\Property(property="phone", type="string", example="11988888888", description="Telefone (opcional)")
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="Operador registrado com sucesso",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string"),
 *             @OA\Property(property="email", type="string"),
 *             @OA\Property(property="requires_verification", type="boolean", example=true)
 *         )
 *     ),
 *     @OA\Response(response=422, description="Dados inválidos")
 * )
 *
 * @OA\Post(
 *     path="/api/v1/operators/login",
 *     summary="Login de operador",
 *     description="Autentica um operador e retorna token de acesso",
 *     operationId="loginOperator",
 *     tags={"Operators"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"email","password"},
 *             @OA\Property(property="email", type="string", format="email", example="maria@email.com"),
 *             @OA\Property(property="password", type="string", format="password", example="password123")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Login realizado com sucesso",
 *         @OA\JsonContent(
 *             @OA\Property(property="token", type="string"),
 *             @OA\Property(
 *                 property="user",
 *                 type="object",
 *                 @OA\Property(property="id", type="integer"),
 *                 @OA\Property(property="name", type="string"),
 *                 @OA\Property(property="email", type="string"),
 *                 @OA\Property(property="type", type="string", example="operator")
 *             )
 *         )
 *     ),
 *     @OA\Response(response=422, description="Credenciais inválidas")
 * )
 *
 * @OA\Post(
 *     path="/api/v1/operators/logout",
 *     summary="Logout de operador",
 *     description="Revoga o token de acesso atual do operador",
 *     operationId="logoutOperator",
 *     tags={"Operators"},
 *     security={{"sanctum":{}}},
 *     @OA\Response(
 *         response=200,
 *         description="Logout realizado com sucesso",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Logout realizado com sucesso")
 *         )
 *     ),
 *     @OA\Response(response=401, description="Não autenticado")
 * )
 *
 * @OA\Get(
 *     path="/api/v1/operators/me",
 *     summary="Obter dados do operador autenticado",
 *     description="Retorna os dados completos do operador autenticado",
 *     operationId="getAuthenticatedOperator",
 *     tags={"Operators"},
 *     security={{"sanctum":{}}},
 *     @OA\Response(
 *         response=200,
 *         description="Dados do operador",
 *         @OA\JsonContent(
 *             @OA\Property(property="id", type="integer"),
 *             @OA\Property(property="name", type="string"),
 *             @OA\Property(property="email", type="string"),
 *             @OA\Property(property="phone", type="string"),
 *             @OA\Property(property="email_verified_at", type="string", format="date-time")
 *         )
 *     ),
 *     @OA\Response(response=401, description="Não autenticado")
 * )
 *
 * @OA\Get(
 *     path="/api/v1/operators/stats",
 *     summary="Obter estatísticas do operador",
 *     description="Retorna estatísticas de vagas gerenciadas pelo operador autenticado",
 *     operationId="getOperatorStats",
 *     tags={"Operators"},
 *     security={{"sanctum":{}}},
 *     @OA\Response(
 *         response=200,
 *         description="Estatísticas do operador",
 *         @OA\JsonContent(
 *             @OA\Property(property="total_spots", type="integer", example=10),
 *             @OA\Property(property="available_spots", type="integer", example=7),
 *             @OA\Property(property="occupied_spots", type="integer", example=3),
 *             @OA\Property(property="reserved_spots", type="integer", example=0),
 *             @OA\Property(property="total_reservations", type="integer", example=50),
 *             @OA\Property(property="active_reservations", type="integer", example=3)
 *         )
 *     ),
 *     @OA\Response(response=401, description="Não autenticado")
 * )
 *
 * @OA\Get(
 *     path="/api/v1/operators",
 *     summary="Listar operadores",
 *     description="Lista todos os operadores cadastrados",
 *     operationId="listOperators",
 *     tags={"Operators"},
 *     security={{"sanctum":{}}},
 *     @OA\Response(
 *         response=200,
 *         description="Lista de operadores",
 *         @OA\JsonContent(
 *             @OA\Property(property="data", type="array", @OA\Items(type="object"))
 *         )
 *     )
 * )
 *
 * @OA\Post(
 *     path="/api/v1/operators",
 *     summary="Criar operador",
 *     description="Cria um novo operador (admin)",
 *     operationId="storeOperator",
 *     tags={"Operators"},
 *     security={{"sanctum":{}}},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"name","email","password"},
 *             @OA\Property(property="name", type="string"),
 *             @OA\Property(property="email", type="string", format="email"),
 *             @OA\Property(property="password", type="string", format="password"),
 *             @OA\Property(property="phone", type="string")
 *         )
 *     ),
 *     @OA\Response(response=201, description="Operador criado"),
 *     @OA\Response(response=422, description="Dados inválidos")
 * )
 *
 * @OA\Get(
 *     path="/api/v1/operators/{id}",
 *     summary="Exibir operador",
 *     description="Retorna dados de um operador específico",
 *     operationId="showOperator",
 *     tags={"Operators"},
 *     security={{"sanctum":{}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(response=200, description="Dados do operador"),
 *     @OA\Response(response=404, description="Operador não encontrado")
 * )
 *
 * @OA\Put(
 *     path="/api/v1/operators/{id}",
 *     summary="Atualizar operador",
 *     description="Atualiza dados de um operador",
 *     operationId="updateOperator",
 *     tags={"Operators"},
 *     security={{"sanctum":{}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             @OA\Property(property="name", type="string"),
 *             @OA\Property(property="email", type="string"),
 *             @OA\Property(property="phone", type="string")
 *         )
 *     ),
 *     @OA\Response(response=200, description="Operador atualizado"),
 *     @OA\Response(response=404, description="Operador não encontrado")
 * )
 *
 * @OA\Delete(
 *     path="/api/v1/operators/{id}",
 *     summary="Deletar operador",
 *     description="Remove um operador do sistema",
 *     operationId="deleteOperator",
 *     tags={"Operators"},
 *     security={{"sanctum":{}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(response=204, description="Operador deletado"),
 *     @OA\Response(response=404, description="Operador não encontrado")
 * )
 *
 * @OA\Get(
 *     path="/api/v1/customers",
 *     summary="Listar clientes",
 *     description="Lista todos os clientes cadastrados",
 *     operationId="listCustomers",
 *     tags={"Customers"},
 *     security={{"sanctum":{}}},
 *     @OA\Response(
 *         response=200,
 *         description="Lista de clientes",
 *         @OA\JsonContent(
 *             @OA\Property(property="data", type="array", @OA\Items(type="object"))
 *         )
 *     )
 * )
 *
 * @OA\Post(
 *     path="/api/v1/customers",
 *     summary="Criar cliente",
 *     description="Cria um novo cliente (admin)",
 *     operationId="storeCustomer",
 *     tags={"Customers"},
 *     security={{"sanctum":{}}},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"name","email","cpf","password"},
 *             @OA\Property(property="name", type="string"),
 *             @OA\Property(property="email", type="string"),
 *             @OA\Property(property="cpf", type="string"),
 *             @OA\Property(property="password", type="string"),
 *             @OA\Property(property="phone", type="string"),
 *             @OA\Property(property="zip_code", type="string"),
 *             @OA\Property(property="street", type="string"),
 *             @OA\Property(property="number", type="string"),
 *             @OA\Property(property="complement", type="string"),
 *             @OA\Property(property="neighborhood", type="string"),
 *             @OA\Property(property="city", type="string"),
 *             @OA\Property(property="state", type="string")
 *         )
 *     ),
 *     @OA\Response(response=201, description="Cliente criado"),
 *     @OA\Response(response=422, description="Dados inválidos")
 * )
 *
 * @OA\Get(
 *     path="/api/v1/customers/{id}",
 *     summary="Exibir cliente",
 *     description="Retorna dados de um cliente específico",
 *     operationId="showCustomer",
 *     tags={"Customers"},
 *     security={{"sanctum":{}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(response=200, description="Dados do cliente"),
 *     @OA\Response(response=404, description="Cliente não encontrado")
 * )
 *
 * @OA\Put(
 *     path="/api/v1/customers/{id}",
 *     summary="Atualizar cliente",
 *     description="Atualiza dados de um cliente",
 *     operationId="updateCustomer",
 *     tags={"Customers"},
 *     security={{"sanctum":{}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             @OA\Property(property="name", type="string"),
 *             @OA\Property(property="email", type="string"),
 *             @OA\Property(property="phone", type="string"),
 *             @OA\Property(property="zip_code", type="string"),
 *             @OA\Property(property="street", type="string"),
 *             @OA\Property(property="number", type="string"),
 *             @OA\Property(property="complement", type="string"),
 *             @OA\Property(property="neighborhood", type="string"),
 *             @OA\Property(property="city", type="string"),
 *             @OA\Property(property="state", type="string")
 *         )
 *     ),
 *     @OA\Response(response=200, description="Cliente atualizado"),
 *     @OA\Response(response=404, description="Cliente não encontrado")
 * )
 *
 * @OA\Delete(
 *     path="/api/v1/customers/{id}",
 *     summary="Deletar cliente",
 *     description="Remove um cliente do sistema",
 *     operationId="deleteCustomer",
 *     tags={"Customers"},
 *     security={{"sanctum":{}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(response=204, description="Cliente deletado"),
 *     @OA\Response(response=404, description="Cliente não encontrado")
 * )
 *
 * @OA\Get(
 *     path="/api/v1/vehicles/{id}",
 *     summary="Exibir veículo",
 *     description="Retorna dados de um veículo específico",
 *     operationId="showVehicle",
 *     tags={"Vehicles"},
 *     security={{"sanctum":{}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(response=200, description="Dados do veículo"),
 *     @OA\Response(response=404, description="Veículo não encontrado")
 * )
 *
 * @OA\Put(
 *     path="/api/v1/vehicles/{id}",
 *     summary="Atualizar veículo",
 *     description="Atualiza dados de um veículo",
 *     operationId="updateVehicle",
 *     tags={"Vehicles"},
 *     security={{"sanctum":{}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             @OA\Property(property="license_plate", type="string"),
 *             @OA\Property(property="brand", type="string"),
 *             @OA\Property(property="model", type="string"),
 *             @OA\Property(property="color", type="string"),
 *             @OA\Property(property="type", type="string", enum={"car","motorcycle","truck"})
 *         )
 *     ),
 *     @OA\Response(response=200, description="Veículo atualizado"),
 *     @OA\Response(response=404, description="Veículo não encontrado")
 * )
 *
 * @OA\Delete(
 *     path="/api/v1/vehicles/{id}",
 *     summary="Deletar veículo",
 *     description="Remove um veículo do sistema",
 *     operationId="deleteVehicle",
 *     tags={"Vehicles"},
 *     security={{"sanctum":{}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(response=204, description="Veículo deletado"),
 *     @OA\Response(response=404, description="Veículo não encontrado")
 * )
 *
 * @OA\Get(
 *     path="/api/v1/parking-spots-available",
 *     summary="Listar vagas disponíveis (público)",
 *     description="Lista vagas disponíveis para reserva. Não requer autenticação",
 *     operationId="listAvailableParkingSpots",
 *     tags={"Parking Spots"},
 *     @OA\Response(
 *         response=200,
 *         description="Lista de vagas disponíveis",
 *         @OA\JsonContent(
 *             @OA\Property(property="data", type="array", @OA\Items(type="object"))
 *         )
 *     )
 * )
 *
 * @OA\Get(
 *     path="/api/v1/parking-spots/{id}",
 *     summary="Exibir vaga",
 *     description="Retorna dados de uma vaga específica",
 *     operationId="showParkingSpot",
 *     tags={"Parking Spots"},
 *     security={{"sanctum":{}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(response=200, description="Dados da vaga"),
 *     @OA\Response(response=404, description="Vaga não encontrada")
 * )
 *
 * @OA\Put(
 *     path="/api/v1/parking-spots/{id}",
 *     summary="Atualizar vaga",
 *     description="Atualiza dados de uma vaga",
 *     operationId="updateParkingSpot",
 *     tags={"Parking Spots"},
 *     security={{"sanctum":{}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             @OA\Property(property="number", type="string"),
 *             @OA\Property(property="type", type="string", enum={"regular","vip","disabled"}),
 *             @OA\Property(property="hourly_price", type="number"),
 *             @OA\Property(property="width", type="number"),
 *             @OA\Property(property="length", type="number"),
 *             @OA\Property(property="status", type="string", enum={"available","occupied","reserved"})
 *         )
 *     ),
 *     @OA\Response(response=200, description="Vaga atualizada"),
 *     @OA\Response(response=404, description="Vaga não encontrada")
 * )
 *
 * @OA\Delete(
 *     path="/api/v1/parking-spots/{id}",
 *     summary="Deletar vaga",
 *     description="Remove uma vaga do sistema",
 *     operationId="deleteParkingSpot",
 *     tags={"Parking Spots"},
 *     security={{"sanctum":{}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(response=204, description="Vaga deletada"),
 *     @OA\Response(response=404, description="Vaga não encontrada")
 * )
 *
 * @OA\Get(
 *     path="/api/v1/reservations",
 *     summary="Listar reservas",
 *     description="Lista todas as reservas do cliente ou operador autenticado",
 *     operationId="listReservations",
 *     tags={"Reservations"},
 *     security={{"sanctum":{}}},
 *     @OA\Parameter(
 *         name="status",
 *         in="query",
 *         description="Filtrar por status",
 *         @OA\Schema(type="string", enum={"active","completed","cancelled"})
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Lista de reservas",
 *         @OA\JsonContent(
 *             @OA\Property(property="data", type="array", @OA\Items(type="object"))
 *         )
 *     )
 * )
 *
 * @OA\Get(
 *     path="/api/v1/reservations/{id}",
 *     summary="Exibir reserva",
 *     description="Retorna dados de uma reserva específica",
 *     operationId="showReservation",
 *     tags={"Reservations"},
 *     security={{"sanctum":{}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(response=200, description="Dados da reserva"),
 *     @OA\Response(response=404, description="Reserva não encontrada")
 * )
 *
 * @OA\Post(
 *     path="/api/v1/reservations/{id}/cancel",
 *     summary="Cancelar reserva",
 *     description="Cancela uma reserva ativa. A vaga volta para status available",
 *     operationId="cancelReservation",
 *     tags={"Reservations"},
 *     security={{"sanctum":{}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Reserva cancelada",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Reserva cancelada com sucesso")
 *         )
 *     ),
 *     @OA\Response(response=404, description="Reserva não encontrada"),
 *     @OA\Response(response=400, description="Reserva não pode ser cancelada")
 * )
 *
 * @OA\Post(
 *     path="/api/v1/reservations/{id}/operator-finalize",
 *     summary="Finalizar reserva (operador)",
 *     description="Operador finaliza uma reserva manualmente, calculando o valor",
 *     operationId="operatorFinalizeReservation",
 *     tags={"Reservations"},
 *     security={{"sanctum":{}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             @OA\Property(property="exit_time", type="string", format="date-time")
 *         )
 *     ),
 *     @OA\Response(response=200, description="Reserva finalizada"),
 *     @OA\Response(response=404, description="Reserva não encontrada")
 * )
 *
 * @OA\Get(
 *     path="/api/v1/reservations/search",
 *     summary="Buscar reserva por placa",
 *     description="Busca reservas ativas pelo número da placa do veículo",
 *     operationId="searchReservationByPlate",
 *     tags={"Reservations"},
 *     security={{"sanctum":{}}},
 *     @OA\Parameter(
 *         name="license_plate",
 *         in="query",
 *         required=true,
 *         description="Placa do veículo",
 *         @OA\Schema(type="string", example="ABC1D234")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Reserva encontrada",
 *         @OA\JsonContent(
 *             @OA\Property(property="data", type="object")
 *         )
 *     ),
 *     @OA\Response(response=404, description="Reserva não encontrada")
 * )
 *
 * @OA\Get(
 *     path="/api/v1/reservations/active-by-spot/{spotId}",
 *     summary="Buscar reserva ativa por vaga",
 *     description="Retorna a reserva ativa de uma vaga específica",
 *     operationId="getActiveReservationBySpot",
 *     tags={"Reservations"},
 *     security={{"sanctum":{}}},
 *     @OA\Parameter(
 *         name="spotId",
 *         in="path",
 *         required=true,
 *         description="ID da vaga",
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Reserva ativa encontrada",
 *         @OA\JsonContent(
 *             @OA\Property(property="data", type="object")
 *         )
 *     ),
 *     @OA\Response(response=404, description="Nenhuma reserva ativa encontrada")
 * )
 *
 * @OA\Get(
 *     path="/api/v1/payments/{id}",
 *     summary="Exibir pagamento",
 *     description="Retorna dados de um pagamento específico",
 *     operationId="showPayment",
 *     tags={"Payments"},
 *     security={{"sanctum":{}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(response=200, description="Dados do pagamento"),
 *     @OA\Response(response=404, description="Pagamento não encontrado")
 * )
 *
 * @OA\Delete(
 *     path="/api/v1/payments/{id}",
 *     summary="Deletar pagamento",
 *     description="Remove um pagamento do sistema",
 *     operationId="deletePayment",
 *     tags={"Payments"},
 *     security={{"sanctum":{}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(response=204, description="Pagamento deletado"),
 *     @OA\Response(response=404, description="Pagamento não encontrado")
 * )
 *
 * @OA\Get(
 *     path="/api/v1/email/verify/{id}/{hash}",
 *     summary="Verificar email",
 *     description="Verifica o email através do link enviado por email",
 *     operationId="verifyEmail",
 *     tags={"Authentication"},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="ID do usuário",
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Parameter(
 *         name="hash",
 *         in="path",
 *         required=true,
 *         description="Hash de verificação",
 *         @OA\Schema(type="string")
 *     ),
 *     @OA\Parameter(
 *         name="type",
 *         in="query",
 *         required=true,
 *         description="Tipo de usuário",
 *         @OA\Schema(type="string", enum={"customer","operator"})
 *     ),
 *     @OA\Response(
 *         response=302,
 *         description="Redireciona para página de sucesso/erro no frontend"
 *     ),
 *     @OA\Response(response=400, description="Link inválido ou expirado")
 * )
 *
 * @OA\Get(
 *     path="/api/v1/address/{zipCode}",
 *     summary="Buscar endereço por CEP",
 *     description="Consulta dados de endereço através do CEP (via ViaCEP)",
 *     operationId="getAddressByZipCodePublic",
 *     tags={"Utils"},
 *     @OA\Parameter(
 *         name="zipCode",
 *         in="path",
 *         required=true,
 *         description="CEP com 8 dígitos",
 *         @OA\Schema(type="string", example="01310100")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Dados do endereço",
 *         @OA\JsonContent(
 *             @OA\Property(property="zip_code", type="string"),
 *             @OA\Property(property="street", type="string"),
 *             @OA\Property(property="neighborhood", type="string"),
 *             @OA\Property(property="city", type="string"),
 *             @OA\Property(property="state", type="string")
 *         )
 *     ),
 *     @OA\Response(response=404, description="CEP não encontrado")
 * )
 *
 * @OA\Get(
 *     path="/api/v1/health",
 *     summary="Health check",
 *     description="Endpoint para verificar se a API está funcionando",
 *     operationId="healthCheck",
 *     tags={"Utils"},
 *     @OA\Response(
 *         response=200,
 *         description="API está funcionando",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="string", example="ok"),
 *             @OA\Property(property="timestamp", type="string", format="date-time")
 *         )
 *     )
 * )
 */
class ApiDocumentation extends Controller
{
    /**
     * Este controller existe APENAS para documentação Swagger.
     * NÃO adicione métodos ou lógica de negócio aqui.
     */
}
