<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\IncidenciasRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: IncidenciasRepository::class)]
#[ApiResource]
class Incidencias
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $incidencia = null;

    #[ORM\Column]
    private ?int $registroCodigo = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIncidencia(): ?string
    {
        return $this->incidencia;
    }

    public function setIncidencia(?string $incidencia): static
    {
        $this->incidencia = $incidencia;

        return $this;
    }

    public function getRegistroCodigo(): ?int
    {
        return $this->registroCodigo;
    }

    public function setRegistroCodigo(int $registroCodigo): static
    {
        $this->registroCodigo = $registroCodigo;

        return $this;
    }
}
