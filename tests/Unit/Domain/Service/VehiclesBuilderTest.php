<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Service;

use Domain\Entity\Vehicle;
use Domain\Repository\VehicleRepositoryInterface;
use Domain\Service\VehiclesBuilder;
use Domain\ValueObject\RegistrationNumber;
use Domain\ValueObject\VehicleType;
use PHPUnit\Framework\TestCase;

class VehiclesBuilderTest extends TestCase
{
    private VehicleRepositoryInterface $vehicleRepository;
    private VehiclesBuilder $vehiclesBuilder;

    protected function setUp(): void
    {
        $this->vehicleRepository = $this->createMock(VehicleRepositoryInterface::class);
        $this->vehiclesBuilder = new VehiclesBuilder($this->vehicleRepository);
    }

    public function testGetList(): void
    {
        // Arrange
        $vehicle = Vehicle::create(
            new RegistrationNumber('ABC123'),
            'Toyota',
            'Corolla',
            VehicleType::PASSENGER
        );
        $vehicle->setId(1);
        
        $this->vehicleRepository->method('getList')
            ->willReturn([$vehicle]);

        // Act
        $result = $this->vehiclesBuilder->getList();

        // Assert
        $this->assertCount(1, $result);
        $this->assertEquals(1, $result[0]->id);
        $this->assertEquals('ABC123', $result[0]->registrationNumber);
        $this->assertEquals('Toyota', $result[0]->brand);
        $this->assertEquals('Corolla', $result[0]->model);
        $this->assertEquals('passenger', $result[0]->type);
    }

    public function testEntityToDTO(): void
    {
        // Arrange
        $vehicle = Vehicle::create(
            new RegistrationNumber('XYZ789'),
            'Volvo',
            'XC90',
            VehicleType::TRUCK
        );
        $vehicle->setId(2);
        
        $reflectionClass = new \ReflectionClass(VehiclesBuilder::class);
        $method = $reflectionClass->getMethod('entityToDTO');
        $method->setAccessible(true);

        // Act
        $result = $method->invoke($this->vehiclesBuilder, $vehicle);

        // Assert
        $this->assertEquals(2, $result->id);
        $this->assertEquals('XYZ789', $result->registrationNumber);
        $this->assertEquals('Volvo', $result->brand);
        $this->assertEquals('XC90', $result->model);
        $this->assertEquals('truck', $result->type);
        $this->assertMatchesRegularExpression('/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}$/', $result->createdAt);
        $this->assertMatchesRegularExpression('/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}$/', $result->updatedAt);
    }
}
