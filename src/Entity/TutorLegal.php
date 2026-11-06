<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\TutorLegalRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TutorLegalRepository::class)]
#[ApiResource]
class TutorLegal
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nombre = null;

    #[ORM\Column(length: 255)]
    private ?string $apellidos = null;

    /**
     * @var Collection<int, Alumnos>
     */
    #[ORM\OneToMany(targetEntity: Alumnos::class, mappedBy: 'tutorLegalCodigo', orphanRemoval: true)]
    private Collection $responsable;

    /**
     * @var Collection<int, Profesor>
     */
    #[ORM\ManyToMany(targetEntity: Profesor::class)]
    private Collection $chatea;

    public function __construct()
    {
        $this->responsable = new ArrayCollection();
        $this->chatea = new ArrayCollection();
    }

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

    /**
     * @return Collection<int, Alumnos>
     */
    public function getResponsable(): Collection
    {
        return $this->responsable;
    }

    public function addResponsable(Alumnos $responsable): static
    {
        if (!$this->responsable->contains($responsable)) {
            $this->responsable->add($responsable);
            $responsable->setTutorLegalCodigo($this);
        }

        return $this;
    }

    public function removeResponsable(Alumnos $responsable): static
    {
        if ($this->responsable->removeElement($responsable)) {
            // set the owning side to null (unless already changed)
            if ($responsable->getTutorLegalCodigo() === $this) {
                $responsable->setTutorLegalCodigo(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Profesor>
     */
    public function getChatea(): Collection
    {
        return $this->chatea;
    }

    public function addChatea(Profesor $chatea): static
    {
        if (!$this->chatea->contains($chatea)) {
            $this->chatea->add($chatea);
        }

        return $this;
    }

    public function removeChatea(Profesor $chatea): static
    {
        $this->chatea->removeElement($chatea);

        return $this;
    }
}
