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

    public function saveVehicle(VehicleDTO $vehicleDTO)
    {
        $existingVehicle = $this->vehicleRepository->findByRegistrationNumber($vehicleDTO->registrationNumber);
        if ($existingVehicle) {
            //Update
        } else {
            $vehicle = Vehicle::create(
                new RegistrationNumber($vehicleDTO->registrationNumber),
                $vehicleDTO->brand,
                $vehicleDTO->model,
                VehicleType::from($vehicleDTO->type)
            );
    
            // 3. Zapis przez repozytorium
            $this->vehicleRepository->persist($vehicle);
    
            return $vehicle;
        }
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
