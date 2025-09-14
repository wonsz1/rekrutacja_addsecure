<?php

namespace Domain\Entity;

use Domain\ValueObject\RegistrationNumber;
use Domain\ValueObject\VehicleType;

class Vehicle
{
    private $id;
    private $registrationNumber;
    private $brand;
    private $model;
    private $type;
    private $createdAt;
    private $updatedAt;

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    public function getRegistrationNumber()
    {
        return $this->registrationNumber;
    }

    public function setRegistrationNumber($registrationNumber)
    {
        $this->registrationNumber = $registrationNumber;
        return $this;
    }

    public function getBrand()
    {
        return $this->brand;
    }

    public function setBrand($brand)
    {
        $this->brand = $brand;
        return $this;
    }

    public function getModel()
    {
        return $this->model;
    }

    public function setModel($model)
    {
        $this->model = $model;
        return $this;
    }

    public function getType()
    {
        return $this->type;
    }

    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }

    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }

    public static function create(
        RegistrationNumber $registrationNumber,
        string $brand,
        string $model,
        VehicleType $vehicleType
    ): self {
        self::validateBrand($brand);
        self::validateModel($model);

        return new self($registrationNumber, $brand, $model, $vehicleType);
    }


    private static function validateBrand(string $brand): void
    {
        if (empty(trim($brand))) {
            throw new \DomainException('Brand cannot be empty');
        }
        
        if (strlen($brand) > 60) {
            throw new \DomainException('Brand cannot exceed 60 characters');
        }
    }

    private static function validateModel(string $model): void
    {
        if (empty(trim($model))) {
            throw new \DomainException('Model cannot be empty');
        }
        
        if (strlen($model) > 60) {
            throw new \DomainException('Model cannot exceed 60 characters');
        }
    }
}
