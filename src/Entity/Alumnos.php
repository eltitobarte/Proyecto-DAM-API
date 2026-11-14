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


    #[ORM\OneToOne(inversedBy: 'alumnoCodigo', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?RegistroDiario $tiene = null;

    #[ORM\ManyToOne(inversedBy: 'pertenece')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Aula $aulaCodigo = null;

    #[ORM\ManyToOne(inversedBy: 'responsable')]
    #[ORM\JoinColumn(nullable: false)]
    private ?TutorLegal $tutorLegalCodigo = null;

    #[ORM\OneToOne(inversedBy: 'alumnoCodigo', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?Comida $recibe = null;

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

    public function getTiene(): ?RegistroDiario
    {
        return $this->tiene;
    }

    public function setTiene(RegistroDiario $tiene): static
    {
        $this->tiene = $tiene;

        return $this;
    }

    public function getAulaCodigo(): ?Aula
    {
        return $this->aulaCodigo;
    }

    public function setAulaCodigo(?Aula $aulaCodigo): static
    {
        $this->aulaCodigo = $aulaCodigo;

        return $this;
    }

    public function getTutorLegalCodigo(): ?TutorLegal
    {
        return $this->tutorLegalCodigo;
    }

    public function setTutorLegalCodigo(?TutorLegal $tutorLegalCodigo): static
    {
        $this->tutorLegalCodigo = $tutorLegalCodigo;

        return $this;
    }

    public function getRecibe(): ?Comida
    {
        return $this->recibe;
    }

    public function setRecibe(Comida $recibe): static
    {
        $this->recibe = $recibe;

        return $this;
    }
}
