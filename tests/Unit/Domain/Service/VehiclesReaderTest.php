<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Service;

use Domain\Entity\Vehicle;
use Domain\Repository\VehicleRepositoryInterface;
use Domain\Service\VehiclesReader;
use Domain\ValueObject\RegistrationNumber;
use Domain\ValueObject\VehicleType;
use PHPUnit\Framework\TestCase;

class VehiclesReaderTest extends TestCase
{
    private VehicleRepositoryInterface $vehicleRepository;
    private VehiclesReader $vehiclesReader;

    protected function setUp(): void
    {
        $this->vehicleRepository = $this->createMock(VehicleRepositoryInterface::class);
        $this->vehiclesReader = new VehiclesReader($this->vehicleRepository);
    }

    public function testGetVehicleByIdReturnsVehicleWhenFound(): void
    {
        // Arrange
        $vehicle = Vehicle::create(
            new RegistrationNumber('ABC123'),
            'Toyota',
            'Corolla',
            VehicleType::PASSENGER
        );
        $vehicle->setId(1);
        
        $this->vehicleRepository->method('getById')
            ->with(1)
            ->willReturn($vehicle);

        // Act
        $result = $this->vehiclesReader->getVehicleById(1);

        // Assert
        $this->assertSame($vehicle, $result);
    }

    public function testGetVehicleByIdReturnsNullWhenNotFound(): void
    {
        // Arrange
        $this->vehicleRepository->method('getById')
            ->with(999)
            ->willReturn(null);

        // Act
        $result = $this->vehiclesReader->getVehicleById(999);

        // Assert
        $this->assertNull($result);
    }
}
