<?php

namespace App\Traits;

use App\Models\APIRequest;

trait ApiRequestTrait
{
    public function createRequest(string $endpoint, string $ip_address, string $method, string $request_description, string $status, string $user_agent)
    {
        $user =  auth('sanctum')->user()
            ? auth('sanctum')->user()
            : auth()->user();

        APIRequest::create([
            'endpoint' => $endpoint,
            'ip_address' => $ip_address,
            'method' => $method,
            'user_id' => $user->id,
            'request_description' => $request_description ?? null,
            // 'request' => $request ?? null,
            // 'parameters' => $parameters ?? null,
            // 'response' => $response ?? null,
            'status' => $status,
            'user_agent' => $user_agent ?? null
        ]);
    }
}
