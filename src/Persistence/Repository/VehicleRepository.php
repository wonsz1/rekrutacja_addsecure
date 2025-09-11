<?php

namespace Persistence\Repository;

use App\SQLiteConnection;
use Domain\Entity\Vehicle;
use Domain\Repository\VehicleRepositoryInterface;

class VehicleRepository implements VehicleRepositoryInterface
{
    private \PDO $pdo;

    public function __construct()
    {
        $this->pdo = (new SQLiteConnection())->connect();
    }

    public function getList()
    {
        $results = $this->pdo->query('SELECT * FROM vehicles');

        $items = [];
        foreach ($results as $row) {
            $items[] = $this->rowToEntity($row);
        }

        return $items;
    }

    public function getById($id)
    {

    }

    public function deleteById($id)
    {

    }

    public function persist(Vehicle $vehicle)
    {

    }

    private function rowToEntity($row)
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
    }
}
