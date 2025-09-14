<?php

namespace Domain\Service;

use Domain\Entity\Vehicle;
use Domain\ValueObject\RegistrationNumber;
use Domain\ValueObject\VehicleType;
use Domain\Repository\VehicleRepositoryInterface;

class VehiclesBuilder
{
    public function __construct(private VehicleRepositoryInterface $vehicleRepository)
    {
    }

    public function getList(): array
    {
        $items = $this->vehicleRepository->getList();

        return array_map([$this, 'entityToDTO'], $items);
    }

    public function createVehicle(
        string $registrationNumber,
        string $brand,
        string $model,
        string $vehicleType
    ): VehicleDTO {
        $registrationNumberVO = new RegistrationNumber($registrationNumber);
        
        $vehicle = Vehicle::create(
            $registrationNumberVO,
            $brand,
            $model,
            VehicleType::from($vehicleType)
        );

        return $this->entityToDTO($vehicle);
    }

    private function entityToDTO(Vehicle $vehicle)
    {
        $vehicleDTO = new VehicleDTO();
        $vehicleDTO->id = $vehicle->getId();
        $vehicleDTO->registrationNumber = $vehicle->getRegistrationNumber()->getValue();
        $vehicleDTO->brand = $vehicle->getBrand();
        $vehicleDTO->model = $vehicle->getModel();
        $vehicleDTO->type = $vehicle->getType()->getValue();
        $vehicleDTO->createdAt = $vehicle->getCreatedAt()->format('Y-m-d H:i:s');
        $vehicleDTO->updatedAt = $vehicle->getUpdatedAt()->format('Y-m-d H:i:s');

        return $vehicleDTO;
    }
}
