<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\AulaRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
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

    /**
     * @var Collection<int, Alumnos>
     */
    #[ORM\OneToMany(targetEntity: Alumnos::class, mappedBy: 'aulaCodigo', orphanRemoval: true)]
    private Collection $pertenece;

    #[ORM\OneToOne(mappedBy: 'imparte', cascade: ['persist', 'remove'])]
    private ?Profesor $profesorCodigo = null;

    public function __construct()
    {
        $this->pertenece = new ArrayCollection();
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

    /**
     * @return Collection<int, Alumnos>
     */
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
            // set the owning side to null (unless already changed)
            if ($pertenece->getAulaCodigo() === $this) {
                $pertenece->setAulaCodigo(null);
            }
        }

        return $this;
    }

    public function getProfesorCodigo(): ?Profesor
    {
        return $this->profesorCodigo;
    }

    public function setProfesorCodigo(Profesor $profesorCodigo): static
    {
        // set the owning side of the relation if necessary
        if ($profesorCodigo->getImparte() !== $this) {
            $profesorCodigo->setImparte($this);
        }

        $this->profesorCodigo = $profesorCodigo;

        return $this;
    }

}
