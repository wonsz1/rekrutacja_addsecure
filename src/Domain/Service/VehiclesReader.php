<?php

namespace Domain\Service;

use Domain\Entity\Vehicle;
use Domain\Repository\VehicleRepositoryInterface;

class VehiclesReader
{
    use EntityToDTOTrait;

    public function __construct(
        private VehicleRepositoryInterface $vehicleRepository,
    ) {
    }

    public function getVehicleById(int $id): ?VehicleDTO
    {
        $vahicle = $this->vehicleRepository->getById($id);
        return $this->entityToDTO($vahicle);
    }
}
