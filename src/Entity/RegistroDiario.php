<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use App\State\RegistroDiarioProcessor;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;

#[ORM\Entity(repositoryClass: RegistroDiarioRepository::class)]
#[ApiResource(operations: [
    new GetCollection(),
    new Get(),
    new Post(processor: RegistroDiarioProcessor::class),
    new Put(processor: RegistroDiarioProcessor::class),
    new Patch(processor: RegistroDiarioProcessor::class),
    new Delete(),
])]
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

    #[ORM\ManyToOne(inversedBy: 'registrosDiarios')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Alumnos $alumno = null;

    /**
     * @var Collection<int, Incidencias>
     */
    #[ORM\OneToMany(targetEntity: Incidencias::class, mappedBy: 'registroDiario', cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $incidencias;

    public function __construct()
    {
        $this->incidencias = new ArrayCollection();
    }

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

    public function getAlumno(): ?Alumnos
    {
        return $this->alumno;
    }

    public function setAlumno(?Alumnos $alumno): static
    {
        $this->alumno = $alumno;
        return $this;
    }

    /** @return Collection<int, Incidencias> */
    public function getIncidencias(): Collection
    {
        return $this->incidencias;
    }

    public function addIncidencia(Incidencias $incidencia): static
    {
        if (!$this->incidencias->contains($incidencia)) {
            $this->incidencias->add($incidencia);
            $incidencia->setRegistroDiario($this);
        }
        return $this;
    }

    public function removeIncidencia(Incidencias $incidencia): static
    {
        if ($this->incidencias->removeElement($incidencia)) {
            if ($incidencia->getRegistroDiario() === $this) {
                $incidencia->setRegistroDiario(null);
            }
        }
        return $this;
    }
}