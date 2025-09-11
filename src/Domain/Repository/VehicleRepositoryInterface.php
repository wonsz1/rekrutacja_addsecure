<?php

namespace Domain\Repository;

use Domain\Entity\Vehicle;

interface VehicleRepositoryInterface
{
    public function getList();

    public function getById($id);

    public function deleteById($id);

    public function persist(Vehicle $vehicle);
}
