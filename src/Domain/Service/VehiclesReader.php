<?php

namespace Domain\Service;

use Domain\Repository\VehicleRepositoryInterface;

class VehiclesReader
{
    public function __construct(private VehicleRepositoryInterface $vehicleRepository)
    {
    }

    public function getVehicleById($id)
    {

    }
}
