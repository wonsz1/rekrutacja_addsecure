<?php

namespace Domain\Service;

use Domain\Entity\Vehicle;
use Domain\Repository\VehicleRepositoryInterface;

class VehiclesReader
{
    public function __construct(
        private VehicleRepositoryInterface $vehicleRepository,
    ) {
    }

    public function getVehicleById(int $id): ?VehicleDTO
    {
        $vehicle = $this->vehicleRepository->getById($id);
        
        if (!$vehicle) {
            return null;
        }
        
        return $this->entityToDTO($vehicle);
    }


    private function entityToDTO(Vehicle $vehicle)
    {
        $vehicleDTO = new VehicleDTO();
        $vehicleDTO->id = $vehicle->getId();
        $vehicleDTO->registrationNumber = $vehicle->getRegistrationNumber();
        $vehicleDTO->brand = $vehicle->getBrand();
        $vehicleDTO->model = $vehicle->getModel();
        $vehicleDTO->type = $vehicle->getType();
        $vehicleDTO->createdAt = $vehicle->getCreatedAt();
        $vehicleDTO->updatedAt = $vehicle->getUpdatedAt();

        return $vehicleDTO;
    }
}
