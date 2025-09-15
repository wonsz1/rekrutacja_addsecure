<?php

declare(strict_types=1);

namespace Tests\Integration\Domain\Entity;

use App\SQLiteConnection;
use Domain\Entity\Vehicle;
use Persistence\Repository\VehicleRepository;
use Domain\ValueObject\RegistrationNumber;
use Domain\ValueObject\VehicleType;
use PHPUnit\Framework\TestCase;

class VehicleIntegrationTest extends TestCase
{
    private static \PDO $pdo;
    private VehicleRepository $repository;
    
    private const TEST_REG_NUMBER = 'ABC123';
    private const TEST_BRAND = 'Toyota';
    private const TEST_MODEL = 'Corolla';
    private const TEST_TYPE = VehicleType::PASSENGER;

    public static function setUpBeforeClass(): void
    {
        // Set up test database connection
        self::$pdo = new \PDO('sqlite::memory:');
        self::$pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        
        // Create test tables
        self::createTestTables();
    }

    protected function setUp(): void
    {
        // Start transaction
        self::$pdo->beginTransaction();
        
        // Create repository instance for each test
        $this->repository = new VehicleRepository(self::$pdo);
    }

    protected function tearDown(): void
    {
        // Rollback transaction after each test
        self::$pdo->rollBack();
    }

    private static function createTestTables(): void
    {
        $sql = 'CREATE TABLE IF NOT EXISTS vehicles (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            registration_number VARCHAR(16) NOT NULL UNIQUE,
            brand VARCHAR(60) NOT NULL,
            model VARCHAR(60) NOT NULL,
            type VARCHAR(20) NOT NULL,
            created_at DATETIME NOT NULL,
            updated_at DATETIME NOT NULL
        )';
        
        self::$pdo->exec($sql);
    }

    public function testCreateAndRetrieveVehicle(): void
    {
        // Arrange
        $vehicle = Vehicle::create(
            new RegistrationNumber(self::TEST_REG_NUMBER),
            self::TEST_BRAND,
            self::TEST_MODEL,
            self::TEST_TYPE
        );

        // Act
        $savedVehicle = $this->repository->persist($vehicle);
        $retrievedVehicle = $this->repository->getById($savedVehicle->getId());

        // Assert
        $this->assertNotNull($retrievedVehicle);
        $this->assertEquals($vehicle->getRegistrationNumber()->getValue(), $retrievedVehicle->getRegistrationNumber()->getValue());
        $this->assertEquals($vehicle->getBrand(), $retrievedVehicle->getBrand());
        $this->assertEquals($vehicle->getModel(), $retrievedVehicle->getModel());
        $this->assertEquals($vehicle->getType(), $retrievedVehicle->getType());
        $this->assertNotNull($retrievedVehicle->getCreatedAt());
        $this->assertNotNull($retrievedVehicle->getUpdatedAt());
    }

    public function testUpdateVehicle(): void
    {
        // Arrange
        $vehicle = Vehicle::create(
            new RegistrationNumber(self::TEST_REG_NUMBER),
            self::TEST_BRAND,
            self::TEST_MODEL,
            self::TEST_TYPE
        );
        
        $savedVehicle = $this->repository->persist($vehicle);
        $originalUpdatedAt = $savedVehicle->getUpdatedAt();
        
        $newBrand = 'Honda';
        $newModel = 'Civic';
        $newType = VehicleType::BUS;
        
        // Act
        $savedVehicle->updateDetails(
            'XYZ789',
            $newBrand,
            $newModel,
            $newType->value
        );
        
        $updatedVehicle = $this->repository->persist($savedVehicle);
        
        // Assert
        $this->assertEquals('XYZ789', $updatedVehicle->getRegistrationNumber()->getValue());
        $this->assertEquals($newBrand, $updatedVehicle->getBrand());
        $this->assertEquals($newModel, $updatedVehicle->getModel());
        $this->assertEquals($newType, $updatedVehicle->getType());
        $this->assertGreaterThan(
            $originalUpdatedAt,
            $updatedVehicle->getUpdatedAt()
        );
    }

    public function testFindByRegistrationNumber(): void
    {
        // Arrange
        $vehicle = Vehicle::create(
            new RegistrationNumber('FIND123'),
            'FindMe',
            'Test',
            VehicleType::TRUCK
        );
        
        $this->repository->persist($vehicle);
        
        // Act
        $foundVehicle = $this->repository->findByRegistrationNumber('FIND123');
        
        // Assert
        $this->assertNotNull($foundVehicle);
        $this->assertEquals('FIND123', $foundVehicle->getRegistrationNumber()->getValue());
    }

    public function testDeleteVehicle(): void
    {
        // Arrange
        $vehicle = Vehicle::create(
            new RegistrationNumber('TODELETE'),
            'ToDelete',
            'Test',
            VehicleType::PASSENGER
        );
        
        $savedVehicle = $this->repository->persist($vehicle);
        $vehicleId = $savedVehicle->getId();
        
        // Act
        $result = $this->repository->deleteById($vehicleId);
        $deletedVehicle = $this->repository->getById($vehicleId);
        
        // Assert
        $this->assertTrue($result);
        $this->assertNull($deletedVehicle);
    }
}
