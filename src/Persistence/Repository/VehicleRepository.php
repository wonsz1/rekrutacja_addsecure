<?php

declare(strict_types=1);

namespace Persistence\Repository;

use App\SQLiteConnection;
use Domain\Entity\Vehicle;
use Domain\Repository\VehicleRepositoryInterface;
use PDO;
use PDOException;
use RuntimeException;

class VehicleRepository implements VehicleRepositoryInterface
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = (new SQLiteConnection())->connect();
    }

    public function getList(): array
    {
        try {
            $results = $this->pdo->query('SELECT * FROM vehicles');

            $items = [];
            foreach ($results as $row) {
                $items[] = $this->rowToEntity($row);
            }

            return $items;
        } catch (PDOException $e) {
            throw new RuntimeException('Failed to fetch vehicles: ' . $e->getMessage(), 0, $e);
        }
    }

    public function getById($id): ?Vehicle
    {
        try {
            $stmt = $this->pdo->prepare('SELECT * FROM vehicles WHERE id = :id');
            $stmt->execute(['id' => $id]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            return $row ? $this->rowToEntity($row) : null;
        } catch (PDOException $e) {
            throw new RuntimeException(sprintf('Failed to fetch vehicle with ID %d: %s', $id, $e->getMessage()), 0, $e);
        }
    }

    public function deleteById($id): bool
    {
        try {
            $stmt = $this->pdo->prepare('DELETE FROM vehicles WHERE id = :id');
            $stmt->execute(['id' => $id]);
            
            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            throw new RuntimeException(sprintf('Failed to delete vehicle with ID %d: %s', $id, $e->getMessage()), 0, $e);
        }
    }

    public function persist(Vehicle $vehicle): Vehicle
    {
        $isNew = $vehicle->getId() === null;
        
        try {
            $this->pdo->beginTransaction();
            
            $data = [
                'registration_number' => $vehicle->getRegistrationNumber(),
                'brand' => $vehicle->getBrand(),
                'model' => $vehicle->getModel(),
                'type' => $vehicle->getType(),
                'updated_at' => (new \DateTimeImmutable())->format('Y-m-d H:i:s')
            ];
            
            if ($isNew) {
                $data['created_at'] = $data['updated_at'];
                $sql = 'INSERT INTO vehicles (registration_number, brand, model, type, created_at, updated_at) 
                        VALUES (:registration_number, :brand, :model, :type, :created_at, :updated_at)';
                $this->pdo->prepare($sql)->execute($data);
                $vehicle->setId((int)$this->pdo->lastInsertId());
            } else {
                $data['id'] = $vehicle->getId();
                $sql = 'UPDATE vehicles 
                        SET registration_number = :registration_number, 
                            brand = :brand, 
                            model = :model, 
                            type = :type, 
                            updated_at = :updated_at 
                        WHERE id = :id';
                $this->pdo->prepare($sql)->execute($data);
            }
            
            $this->pdo->commit();
            return $vehicle;
            
        } catch (PDOException $e) {
            $this->pdo->rollBack();
            throw new RuntimeException('Failed to save vehicle: ' . $e->getMessage(), 0, $e);
        }
    }

    private function rowToEntity(array $row): Vehicle
    {
        return (new Vehicle())
            ->setId($row['id'])
            ->setRegistrationNumber($row['registration_number'])
            ->setBrand($row['brand'])
            ->setModel($row['model'])
            ->setType($row['type'])
            ->setCreatedAt($row['created_at'])
            ->setUpdatedAt($row['updated_at'])
        ;
        // return (new Vehicle(
        //     $row['registration_number'],
        //     $row['brand'],
        //     $row['model'],
        //     $row['type']
        // ))
        //     ->setId((int)$row['id'])
        //     ->setCreatedAt(new \DateTimeImmutable($row['created_at']))
        //     ->setUpdatedAt(new \DateTimeImmutable($row['updated_at']));
    }
}
