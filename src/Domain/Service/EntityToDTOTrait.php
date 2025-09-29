<?php

namespace Domain\Service;

use Domain\Entity\Vehicle;

trait EntityToDTOTrait
{
    private function entityToDTO(Vehicle $vehicle): VehicleDTO
    {
        $vehicleDTO = new VehicleDTO();
        $vehicleDTO->id = $vehicle->getId();
        $vehicleDTO->registrationNumber = $vehicle->getRegistrationNumber()->getValue();
        $vehicleDTO->brand = $vehicle->getBrand();
        $vehicleDTO->model = $vehicle->getModel();
        $vehicleDTO->type = $vehicle->getType()->getValue();
        $vehicleDTO->createdAt = $vehicle->getCreatedAt()->format('Y-m-d H:i');
        $vehicleDTO->updatedAt = $vehicle->getUpdatedAt()->format('Y-m-d H:i');

        return $vehicleDTO;
    }
}
