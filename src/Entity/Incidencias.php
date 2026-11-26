<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\IncidenciasRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;

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

    #[ORM\ManyToOne(inversedBy: 'incidencias')]
    #[ORM\JoinColumn(nullable: false)]
    private ?RegistroDiario $registroDiario = null;

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

    public function getRegistroDiario(): ?RegistroDiario
    {
        return $this->registroDiario;
    }

    public function setRegistroDiario(?RegistroDiario $registroDiario): static
    {
        $this->registroDiario = $registroDiario;
        return $this;
    }
}