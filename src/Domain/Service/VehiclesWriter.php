<?php

namespace Domain\Service;

use Domain\Repository\VehicleRepositoryInterface;
use Domain\Entity\Vehicle;
use Domain\ValueObject\RegistrationNumber;
use Domain\ValueObject\VehicleType;

class VehiclesWriter
{
    public function __construct(private VehicleRepositoryInterface $vehicleRepository)
    {
    }

    public function saveVehicle(string $registrationNumber, string $brand, string $model, string $type)
    {
        $existingVehicle = $this->vehicleRepository->findByRegistrationNumber($registrationNumber);
        if ($existingVehicle) {
            throw new \DomainException(
                "Vehicle with registration {$registrationNumber} already exists"
            );
        }

        $vehicle = Vehicle::create(
            new RegistrationNumber($registrationNumber),
            $brand,
            $model,
            VehicleType::from($type)
        );

        return $this->vehicleRepository->persist($vehicle);
    }

    public function updateVehicle(int $id, string $registrationNumber, string $brand, string $model, string $type)
    {
        $vehicle = $this->vehicleRepository->getById($id);
        $vehicle->updateDetails(
            $registrationNumber,
            $brand,
            $model,
            $type,
        );

        return $this->vehicleRepository->persist($vehicle);
    }

    public function deleteById($id)
    {
        $vehicle = $this->vehicleRepository->getById($id);
        if (!$vehicle) {
            throw new \DomainException("Vehicle with ID {$id} not found");
        }

        $this->vehicleRepository->deleteById($id);
    }
}
