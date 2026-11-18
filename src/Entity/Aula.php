<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\AulaRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\DBAL\Types\Types;

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

    #[ORM\Column]
    private ?int $capacidadMaxima = null;

    /**
     * @var Collection<int, Alumnos>
     */
    #[ORM\OneToMany(targetEntity: Alumnos::class, mappedBy: 'aulaCodigo')]
    private Collection $pertenece;

    /**
     * @var Collection<int, Profesor>
     */
    #[ORM\OneToMany(targetEntity: Profesor::class, mappedBy: 'imparte')]
    private Collection $profesores;

    public function __construct()
    {
        $this->pertenece = new ArrayCollection();
        $this->profesores = new ArrayCollection();
    }

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

    public function getCapacidadMaxima(): ?int
    {
        return $this->capacidadMaxima;
    }

    public function setCapacidadMaxima(int $capacidadMaxima): static
    {
        $this->capacidadMaxima = $capacidadMaxima;
        return $this;
    }

    /** @return Collection<int, Alumnos> */
    public function getPertenece(): Collection
    {
        return $this->pertenece;
    }

    public function addPertenece(Alumnos $pertenece): static
    {
        if (!$this->pertenece->contains($pertenece)) {
            $this->pertenece->add($pertenece);
            $pertenece->setAulaCodigo($this);
        }
        return $this;
    }

    public function removePertenece(Alumnos $pertenece): static
    {
        if ($this->pertenece->removeElement($pertenece)) {
            if ($pertenece->getAulaCodigo() === $this) {
                $pertenece->setAulaCodigo(null);
            }
        }
        return $this;
    }

    /** @return Collection<int, Profesor> */
    public function getProfesores(): Collection
    {
        return $this->profesores;
    }

    public function addProfesor(Profesor $profesor): static
    {
        if (!$this->profesores->contains($profesor)) {
            $this->profesores->add($profesor);
            $profesor->setImparte($this);
        }
        return $this;
    }

    public function removeProfesor(Profesor $profesor): static
    {
        if ($this->profesores->removeElement($profesor)) {
            if ($profesor->getImparte() === $this) {
                $profesor->setImparte(null);
            }
        }
        return $this;
    }
}