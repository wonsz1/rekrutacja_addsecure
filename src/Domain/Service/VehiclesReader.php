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

    public function getVehicleById(int $id): ?Vehicle
    {
        return $this->vehicleRepository->getById($id);
    }
}
