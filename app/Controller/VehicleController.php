<?php

namespace App\Controller;

use Domain\Service\VehiclesBuilder;
use Persistence\Repository\VehicleRepository;
use Symfony\Component\HttpFoundation\{JsonResponse, Request, Response};

class VehicleController extends BaseController
{
    public function index(): Response
    {
        ob_start();
        include __DIR__ . '/../views/index.php';
        return (new Response(ob_get_clean()))->send();
    }

    public function list(): JsonResponse
    {
        $results = (new VehiclesBuilder(new VehicleRepository()))->getList();

        return $this->toJsonResponse(['results' => $results]);
    }

    public function save(int $id, Request $request): JsonResponse
    {
        $content = json_decode($request->getContent());
        return $this->toJsonResponse([$id, $content]);
    }

    public function delete(int $id): JsonResponse
    {
        return $this->toJsonResponse([$id]);
    }
}
