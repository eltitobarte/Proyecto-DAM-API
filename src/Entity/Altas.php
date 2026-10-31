<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\AltasRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AltasRepository::class)]
#[ApiResource]
class Altas
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTime $fechaAlta = null;

    #[ORM\Column(length: 255)]
    private ?string $estado = null;

    #[ORM\Column]
    private ?int $idAdmin = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFechaAlta(): ?\DateTime
    {
        return $this->fechaAlta;
    }

    public function setFechaAlta(\DateTime $fechaAlta): static
    {
        $this->fechaAlta = $fechaAlta;

        return $this;
    }

    public function getEstado(): ?string
    {
        return $this->estado;
    }

    public function setEstado(string $estado): static
    {
        $this->estado = $estado;

        return $this;
    }

    public function getIdAdmin(): ?int
    {
        return $this->idAdmin;
    }

    public function setIdAdmin(int $idAdmin): static
    {
        $this->idAdmin = $idAdmin;

        return $this;
    }
}
