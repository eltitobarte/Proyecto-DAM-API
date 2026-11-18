<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\AlergiasRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;

#[ORM\Entity(repositoryClass: AlergiasRepository::class)]
#[ApiResource]
class Alergias
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $alergia = null;

    #[ORM\ManyToOne(inversedBy: 'alergias')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Alumnos $alumno = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAlergia(): ?string
    {
        return $this->alergia;
    }

    public function setAlergia(?string $alergia): static
    {
        $this->alergia = $alergia;
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