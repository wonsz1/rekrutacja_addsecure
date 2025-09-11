<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;

class BaseController
{
    protected function toJsonResponse(array $response, int $status = 200): JsonResponse
    {
        return (new JsonResponse($response, $status))->send();
    }
}
