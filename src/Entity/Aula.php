<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\AulaRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AulaRepository::class)]
#[ApiResource]
class Aula
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $aulaNombre = null;

    #[ORM\Column(length: 255)]
    private ?string $profesorNombre = null;

    #[ORM\Column]
    private ?int $idProfesor = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAulaNombre(): ?string
    {
        return $this->aulaNombre;
    }

    public function setAulaNombre(string $aulaNombre): static
    {
        $this->aulaNombre = $aulaNombre;

        return $this;
    }

    public function getProfesorNombre(): ?string
    {
        return $this->profesorNombre;
    }

    public function setProfesorNombre(string $profesorNombre): static
    {
        $this->profesorNombre = $profesorNombre;

        return $this;
    }

    public function getIdProfesor(): ?int
    {
        return $this->idProfesor;
    }

    public function setIdProfesor(int $idProfesor): static
    {
        $this->idProfesor = $idProfesor;

        return $this;
    }
}
