<?php

declare(strict_types=1);

namespace App\Application\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

final class ViaCepService
{
    private const API_URL = 'https://viacep.com.br/ws';
    private const CACHE_TTL = 86400; // 24 hours

    public function getAddress(string $zipCode): ?array
    {
        // Remove non-numeric characters
        $zipCode = preg_replace('/\D/', '', $zipCode);

        // Validate zip code format
        if (strlen($zipCode) !== 8) {
            throw new \InvalidArgumentException('CEP deve ter 8 dígitos');
        }

        // Check cache first
        $cacheKey = "viacep_{$zipCode}";
        
        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($zipCode) {
            try {
                $response = Http::timeout(10)
                    ->get(self::API_URL . "/{$zipCode}/json/");

                if (!$response->successful()) {
                    throw new \RuntimeException('Erro ao consultar CEP');
                }

                $data = $response->json();

                // Check if CEP was not found
                if (isset($data['erro']) && $data['erro'] === true) {
                    return null;
                }

                return [
                    'zip_code' => $data['cep'] ?? null,
                    'street' => $data['logradouro'] ?? null,
                    'complement' => $data['complemento'] ?? null,
                    'neighborhood' => $data['bairro'] ?? null,
                    'city' => $data['localidade'] ?? null,
                    'state' => $data['uf'] ?? null,
                    'ibge' => $data['ibge'] ?? null,
                ];
            } catch (\Exception $e) {
                throw new \RuntimeException('Erro ao buscar endereço: ' . $e->getMessage());
            }
        });
    }

    public function clearCache(string $zipCode): bool
    {
        $zipCode = preg_replace('/\D/', '', $zipCode);
        $cacheKey = "viacep_{$zipCode}";
        
        return Cache::forget($cacheKey);
    }
}
