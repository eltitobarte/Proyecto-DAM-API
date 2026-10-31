<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\RegistroDiarioRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RegistroDiarioRepository::class)]
#[ApiResource]
class RegistroDiario
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTime $fecha = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $comida = null;

    #[ORM\Column]
    private ?int $alumnoCodigo = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFecha(): ?\DateTime
    {
        return $this->fecha;
    }

    public function setFecha(\DateTime $fecha): static
    {
        $this->fecha = $fecha;

        return $this;
    }

    public function getComida(): ?string
    {
        return $this->comida;
    }

    public function setComida(?string $comida): static
    {
        $this->comida = $comida;

        return $this;
    }

    public function getAlumnoCodigo(): ?int
    {
        return $this->alumnoCodigo;
    }

    public function setAlumnoCodigo(int $alumnoCodigo): static
    {
        $this->alumnoCodigo = $alumnoCodigo;

        return $this;
    }
}
