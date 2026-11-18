<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use App\State\BajaProcessor;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;

#[ORM\Entity(repositoryClass: BajasRepository::class)]
#[ApiResource(operations: [
    new GetCollection(),
    new Get(),
    new Post(processor: BajaProcessor::class),
    new Put(processor: BajaProcessor::class),
    new Patch(processor: BajaProcessor::class),
    new Delete(),
])]
class Bajas
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTime $fechaBaja = null;

    #[ORM\Column(length: 255)]
    private ?string $estado = null;

    #[ORM\ManyToOne(inversedBy: 'gestionBajas')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Administracion $idAdmin = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Alumnos $alumno = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFechaBaja(): ?\DateTime
    {
        return $this->fechaBaja;
    }

    public function setFechaBaja(\DateTime $fechaBaja): static
    {
        $this->fechaBaja = $fechaBaja;
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

    public function getIdAdmin(): ?Administracion
    {
        return $this->idAdmin;
    }

    public function setIdAdmin(?Administracion $idAdmin): static
    {
        $this->idAdmin = $idAdmin;
        return $this;
    }

    public function getAlumno(): ?Alumnos
    {
        return $this->alumno;
    }

    public function setAlumno(?Alumnos $alumno): static
    {
        $this->alumno = $alumno;
        return $this;
    }
}