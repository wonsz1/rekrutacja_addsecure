<?php

namespace Domain\Service;

use Domain\Repository\VehicleRepositoryInterface;

class VehiclesWriter
{
    public function __construct(private VehicleRepositoryInterface $vehicleRepository)
    {
    }

    public function saveVehicle(VehicleDTO $vehicleDTO)
    {

    }

    public function deleteById($id)
    {

    }
}
