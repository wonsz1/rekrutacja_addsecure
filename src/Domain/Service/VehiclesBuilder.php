<?php

namespace Domain\Service;

use Domain\Entity\Vehicle;
use Domain\Repository\VehicleRepositoryInterface;

class VehiclesBuilder
{
    public function __construct(private VehicleRepositoryInterface $vehicleRepository)
    {
    }

    public function getList()
    {
        $items = $this->vehicleRepository->getList();

        return array_map([$this, 'entityToDTO'], $items);
    }

    private function entityToDTO(Vehicle $vehicle)
    {
        $vehicleDTO = new VehicleDTO();
        $vehicleDTO->id = $vehicle->getId();
        $vehicleDTO->registrationNumber = $vehicle->getId();
        $vehicleDTO->brand = $vehicle->getBrand();
        $vehicleDTO->model = $vehicle->getModel();
        $vehicleDTO->type = $vehicle->getType();
        $vehicleDTO->createdAt = $vehicle->getCreatedAt();
        $vehicleDTO->updatedAt = $vehicle->getUpdatedAt();

        return $vehicleDTO;
    }
}
