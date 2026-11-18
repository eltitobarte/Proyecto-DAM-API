<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\NecesidadesApoyoEdRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;

#[ORM\Entity(repositoryClass: NecesidadesApoyoEdRepository::class)]
#[ApiResource]
class NecesidadesApoyoEd
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $necesidadApoyo = null;

    #[ORM\ManyToOne(inversedBy: 'necesidadesApoyo')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Alumnos $alumno = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNecesidadApoyo(): ?string
    {
        return $this->necesidadApoyo;
    }

    public function setNecesidadApoyo(string $necesidadApoyo): static
    {
        $this->necesidadApoyo = $necesidadApoyo;
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