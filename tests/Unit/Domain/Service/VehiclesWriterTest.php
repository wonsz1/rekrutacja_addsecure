<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Service;

use Domain\Entity\Vehicle;
use Domain\Repository\VehicleRepositoryInterface;
use Domain\Service\VehiclesWriter;
use Domain\ValueObject\RegistrationNumber;
use Domain\ValueObject\VehicleType;
use PHPUnit\Framework\TestCase;

class VehiclesWriterTest extends TestCase
{
    private VehicleRepositoryInterface $vehicleRepository;
    private VehiclesWriter $vehiclesWriter;

    protected function setUp(): void
    {
        $this->vehicleRepository = $this->createMock(VehicleRepositoryInterface::class);
        $this->vehiclesWriter = new VehiclesWriter($this->vehicleRepository);
    }

    public function testSaveVehicle(): void
    {
        // Arrange
        $registrationNumber = 'ABC123';
        $brand = 'Toyota';
        $model = 'Corolla';
        $type = 'passenger';
        
        $expectedVehicle = Vehicle::create(
            new RegistrationNumber($registrationNumber),
            $brand,
            $model,
            VehicleType::from($type)
        );
        $expectedVehicle->setId(1);

        $this->vehicleRepository->method('findByRegistrationNumber')
            ->with($registrationNumber)
            ->willReturn(null);

        $this->vehicleRepository->expects($this->once())
            ->method('persist')
            ->willReturn($expectedVehicle);

        // Act
        $result = $this->vehiclesWriter->saveVehicle($registrationNumber, $brand, $model, $type);

        // Assert
        $this->assertSame($expectedVehicle, $result);
    }

    public function testSaveVehicleThrowsExceptionWhenRegistrationNumberExists(): void
    {
        // Arrange
        $registrationNumber = 'ABC123';
        $existingVehicle = Vehicle::create(
            new RegistrationNumber($registrationNumber),
            'Honda',
            'Civic',
            VehicleType::PASSENGER
        );
        
        $this->vehicleRepository->method('findByRegistrationNumber')
            ->with($registrationNumber)
            ->willReturn($existingVehicle);

        // Assert
        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage("Vehicle with registration {$registrationNumber} already exists");

        // Act
        $this->vehiclesWriter->saveVehicle($registrationNumber, 'Toyota', 'Corolla', 'passenger');
    }

    public function testUpdateVehicle(): void
    {
        // Arrange
        $id = 1;
        $registrationNumber = 'ABC123';
        $brand = 'Toyota';
        $model = 'Corolla';
        $type = 'passenger';
        
        $existingVehicle = Vehicle::create(
            new RegistrationNumber('XYZ789'),
            'Honda',
            'Civic',
            VehicleType::PASSENGER
        );
        $existingVehicle->setId($id);

        $this->vehicleRepository->method('getById')
            ->with($id)
            ->willReturn($existingVehicle);

        $this->vehicleRepository->expects($this->once())
            ->method('persist')
            ->willReturnCallback(function (Vehicle $vehicle) use ($existingVehicle) {
                $existingVehicle->updateDetails(
                    $vehicle->getRegistrationNumber()->getValue(),
                    $vehicle->getBrand(),
                    $vehicle->getModel(),
                    $vehicle->getType()->getValue()
                );
                return $existingVehicle;
            });

        // Act
        $result = $this->vehiclesWriter->updateVehicle($id, $registrationNumber, $brand, $model, $type);

        // Assert
        $this->assertSame($existingVehicle, $result);
        $this->assertEquals($registrationNumber, $existingVehicle->getRegistrationNumber()->getValue());
        $this->assertEquals($brand, $existingVehicle->getBrand());
        $this->assertEquals($model, $existingVehicle->getModel());
        $this->assertEquals($type, $existingVehicle->getType()->getValue());
    }

    public function testDeleteById(): void
    {
        // Arrange
        $id = 1;
        $vehicle = Vehicle::create(
            new RegistrationNumber('ABC123'),
            'Toyota',
            'Corolla',
            VehicleType::PASSENGER
        );
        $vehicle->setId($id);

        $this->vehicleRepository->method('getById')
            ->with($id)
            ->willReturn($vehicle);

        $this->vehicleRepository->expects($this->once())
            ->method('deleteById')
            ->with($id);

        // Act & Assert (should not throw exception)
        $this->vehiclesWriter->deleteById($id);
    }

    public function testDeleteByIdThrowsExceptionWhenVehicleNotFound(): void
    {
        // Arrange
        $nonExistentId = 999;
        
        $this->vehicleRepository->method('getById')
            ->with($nonExistentId)
            ->willReturn(null);

        // Assert
        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage("Vehicle with ID {$nonExistentId} not found");

        // Act
        $this->vehiclesWriter->deleteById($nonExistentId);
    }
}
