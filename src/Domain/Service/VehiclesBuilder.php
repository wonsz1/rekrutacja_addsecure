<?php

namespace Domain\Service;

use Domain\Repository\VehicleRepositoryInterface;

class VehiclesBuilder
{
    use EntityToDTOTrait;
    
    public function __construct(private VehicleRepositoryInterface $vehicleRepository)
    {
    }

    public function getList(): array
    {
        $items = $this->vehicleRepository->getList();

        return array_map([$this, 'entityToDTO'], $items);
    }
}
