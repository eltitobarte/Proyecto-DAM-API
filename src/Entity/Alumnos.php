<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\AlumnosRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AlumnosRepository::class)]
#[ApiResource]
class Alumnos
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nombre = null;

    #[ORM\Column(length: 255)]
    private ?string $apellidos = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTime $fechaNacimiento = null;

    #[ORM\Column]
    private ?int $aulaCodigo = null;

    #[ORM\Column]
    private ?int $tutorLegalCodigo = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNombre(): ?string
    {
        return $this->nombre;
    }

    public function setNombre(string $nombre): static
    {
        $this->nombre = $nombre;

        return $this;
    }

    public function getApellidos(): ?string
    {
        return $this->apellidos;
    }

    public function setApellidos(string $apellidos): static
    {
        $this->apellidos = $apellidos;

        return $this;
    }

    public function getFechaNacimiento(): ?\DateTime
    {
        return $this->fechaNacimiento;
    }

    public function setFechaNacimiento(\DateTime $fechaNacimiento): static
    {
        $this->fechaNacimiento = $fechaNacimiento;

        return $this;
    }

    public function getAulaCodigo(): ?int
    {
        return $this->aulaCodigo;
    }

    public function setAulaCodigo(int $aulaCodigo): static
    {
        $this->aulaCodigo = $aulaCodigo;

        return $this;
    }

    public function getTutorLegalCodigo(): ?int
    {
        return $this->tutorLegalCodigo;
    }

    public function setTutorLegalCodigo(int $tutorLegalCodigo): static
    {
        $this->tutorLegalCodigo = $tutorLegalCodigo;

        return $this;
    }
}
