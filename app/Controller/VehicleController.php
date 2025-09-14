<?php

declare(strict_types=1);

namespace App\Controller;

use Domain\Exception\NotFoundHttpException;
use Domain\Service\VehiclesBuilder;
use Domain\Service\VehiclesReader;
use Domain\Service\VehiclesWriter;
use Persistence\Repository\VehicleRepository;
use Symfony\Component\HttpFoundation\{JsonResponse, Request, Response};

class VehicleController extends BaseController
{
    private VehicleRepository $vehicleRepository;
    private VehiclesBuilder $vehiclesBuilder;
    private VehiclesWriter $vehicleWriter;
    private VehiclesReader $vehicleReader;

    public function __construct()
    {
        $this->vehicleRepository = new VehicleRepository();
        $this->vehiclesBuilder = new VehiclesBuilder($this->vehicleRepository);
        $this->vehicleWriter = new VehiclesWriter($this->vehicleRepository);
        $this->vehicleReader = new VehiclesReader($this->vehicleRepository);
    }

    public function index(): Response
    {
        ob_start();
        include __DIR__ . '/../views/index.php';
        return (new Response(ob_get_clean()))->send();
    }

    public function list(): JsonResponse
    {
        try {
            $vehicles = $this->vehiclesBuilder->getList();
            return $this->toJsonResponse([
                'success' => true,
                'data' => $vehicles
            ]);
        } catch (\Exception $e) {
            return $this->toJsonResponse([
                'success' => false,
                'error' => 'Failed to fetch vehicles',
                'message' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function save(int $id, Request $request): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);
            
            if ($id > 0) {
                // Update existing vehicle
                $vehicle = $this->vehicleReader->getVehicleById($id);
                if (!$vehicle) {
                    throw new NotFoundHttpException('Vehicle not found');
                }
                $vehicle = $this->vehicleWriter->saveVehicle(
                    $vehicle
                );
            } else {
                // Create new vehicle
                $vehicle = $this->vehiclesBuilder->createVehicle(
                    $data['registrationNumber'],
                    $data['brand'],
                    $data['model'],
                    $data['type']
                );
                $savedVehicle = $this->vehicleWriter->saveVehicle(
                    $vehicle
                );
            }
            
            return $this->toJsonResponse([
                'success' => true,
                'id' => $savedVehicle->getId(),
                'message' => $id ? 'Vehicle updated successfully' : 'Vehicle created successfully'
            ], $id ? Response::HTTP_OK : Response::HTTP_CREATED);
            
        } catch (\InvalidArgumentException $e) {
            return $this->toJsonResponse([
                'success' => false,
                'error' => 'Validation failed',
                'message' => $e->getMessage()
            ], Response::HTTP_BAD_REQUEST);
        } catch (NotFoundHttpException $e) {
            return $this->toJsonResponse([
                'success' => false,
                'error' => $e->getMessage()
            ], Response::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            return $this->toJsonResponse([
                'success' => false,
                'error' => 'Failed to save vehicle',
                'message' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function delete(int $id): JsonResponse
    {
        try {
            $this->vehicleWriter->deleteById($id);
            
            return $this->toJsonResponse([
                'success' => true,
                'message' => 'Vehicle deleted successfully'
            ]);
        } catch (\DomainException $e) {
            return $this->toJsonResponse([
                'success' => false,
                'error' => $e->getMessage()
            ], Response::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            return $this->toJsonResponse([
                'success' => false,
                'error' => 'Failed to delete vehicle',
                'message' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
