<?php

namespace App\Entity;

use App\Repository\ComidaRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ComidaRepository::class)]
class Comida
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTime $fecha = null;

    #[ORM\OneToOne(mappedBy: 'recibe', cascade: ['persist', 'remove'])]
    private ?Alumnos $alumnoCodigo = null;

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

    public function getAlumnoCodigo(): ?Alumnos
    {
        return $this->alumnoCodigo;
    }

    public function setAlumnoCodigo(Alumnos $alumnoCodigo): static
    {
        // set the owning side of the relation if necessary
        if ($alumnoCodigo->getRecibe() !== $this) {
            $alumnoCodigo->setRecibe($this);
        }

        $this->alumnoCodigo = $alumnoCodigo;

        return $this;
    }
}
