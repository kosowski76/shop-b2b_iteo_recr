<?php
namespace App\Domain\Entity;

use Symfony\Component\Uid\Uuid;

abstract class Entity
{
    public Uuid $Id;

    public function __construct()
    {
        $this->Id = Uuid::v7();
    }
    public function getId(): Uuid
    {
        return $this->Id;
    }
    public function setId(Uuid $Id): void
    {
        $this->Id = $Id;
    }
}