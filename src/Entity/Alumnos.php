<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use App\State\AlumnoProcessor;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: AlumnosRepository::class)]
#[ApiResource(operations: [
    new GetCollection(),
    new Get(),
    new Post(processor: AlumnoProcessor::class),
    new Delete(),
])]
class Alumnos
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'El nombre del alumno es obligatorio.')]
    #[Assert\Length(max: 255, maxMessage: 'El nombre no puede superar los {{ limit }} caracteres.')]
    private ?string $nombre = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Los apellidos del alumno son obligatorios.')]
    #[Assert\Length(max: 255, maxMessage: 'Los apellidos no pueden superar los {{ limit }} caracteres.')]
    private ?string $apellidos = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Assert\NotNull(message: 'La fecha de nacimiento es obligatoria.')]
    #[Assert\LessThanOrEqual(value: 'today', message: 'La fecha de nacimiento no puede ser futura.')]
    private ?\DateTime $fechaNacimiento = null;

    #[ORM\Column]
    private bool $activo = true;

    #[ORM\ManyToOne(inversedBy: 'pertenece')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull(message: 'El aula del alumno es obligatoria.')]
    private ?Aula $aulaCodigo = null;

    #[ORM\ManyToOne(inversedBy: 'responsable')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull(message: 'El tutor legal del alumno es obligatorio.')]
    private ?TutorLegal $tutorLegalCodigo = null;

    /**
     * @var Collection<int, RegistroDiario>
     */
    #[ORM\OneToMany(targetEntity: RegistroDiario::class, mappedBy: 'alumno', orphanRemoval: true)]
    private Collection $registrosDiarios;

    /**
     * @var Collection<int, Comida>
     */
    #[ORM\OneToMany(targetEntity: Comida::class, mappedBy: 'alumno', orphanRemoval: true)]
    private Collection $comidas;

    /**
     * @var Collection<int, Alergias>
     */
    #[ORM\OneToMany(targetEntity: Alergias::class, mappedBy: 'alumno', cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $alergias;

    /**
     * @var Collection<int, NecesidadesApoyoEd>
     */
    #[ORM\OneToMany(targetEntity: NecesidadesApoyoEd::class, mappedBy: 'alumno', cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $necesidadesApoyo;

    public function __construct()
    {
        $this->registrosDiarios = new ArrayCollection();
        $this->comidas = new ArrayCollection();
        $this->alergias = new ArrayCollection();
        $this->necesidadesApoyo = new ArrayCollection();
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

    public function getFechaNacimiento(): ?\DateTime
    {
        return $this->fechaNacimiento;
    }

    public function setFechaNacimiento(\DateTime $fechaNacimiento): static
    {
        $this->fechaNacimiento = $fechaNacimiento;
        return $this;
    }

    public function isActivo(): bool
    {
        return $this->activo;
    }

    public function setActivo(bool $activo): static
    {
        $this->activo = $activo;
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

    /** @return Collection<int, RegistroDiario> */
    public function getRegistrosDiarios(): Collection
    {
        return $this->registrosDiarios;
    }

    public function addRegistroDiario(RegistroDiario $registroDiario): static
    {
        if (!$this->registrosDiarios->contains($registroDiario)) {
            $this->registrosDiarios->add($registroDiario);
            $registroDiario->setAlumno($this);
        }
        return $this;
    }

    public function removeRegistroDiario(RegistroDiario $registroDiario): static
    {
        if ($this->registrosDiarios->removeElement($registroDiario)) {
            if ($registroDiario->getAlumno() === $this) {
                $registroDiario->setAlumno(null);
            }
        }
        return $this;
    }

    /** @return Collection<int, Comida> */
    public function getComidas(): Collection
    {
        return $this->comidas;
    }

    public function addComida(Comida $comida): static
    {
        if (!$this->comidas->contains($comida)) {
            $this->comidas->add($comida);
            $comida->setAlumno($this);
        }
        return $this;
    }

    public function removeComida(Comida $comida): static
    {
        if ($this->comidas->removeElement($comida)) {
            if ($comida->getAlumno() === $this) {
                $comida->setAlumno(null);
            }
        }
        return $this;
    }

    /** @return Collection<int, Alergias> */
    public function getAlergias(): Collection
    {
        return $this->alergias;
    }

    public function addAlergia(Alergias $alergia): static
    {
        if (!$this->alergias->contains($alergia)) {
            $this->alergias->add($alergia);
            $alergia->setAlumno($this);
        }
        return $this;
    }

    public function removeAlergia(Alergias $alergia): static
    {
        if ($this->alergias->removeElement($alergia)) {
            if ($alergia->getAlumno() === $this) {
                $alergia->setAlumno(null);
            }
        }
        return $this;
    }

    /** @return Collection<int, NecesidadesApoyoEd> */
    public function getNecesidadesApoyo(): Collection
    {
        return $this->necesidadesApoyo;
    }

    public function addNecesidadApoyo(NecesidadesApoyoEd $necesidadApoyo): static
    {
        if (!$this->necesidadesApoyo->contains($necesidadApoyo)) {
            $this->necesidadesApoyo->add($necesidadApoyo);
            $necesidadApoyo->setAlumno($this);
        }
        return $this;
    }

    public function removeNecesidadApoyo(NecesidadesApoyoEd $necesidadApoyo): static
    {
        if ($this->necesidadesApoyo->removeElement($necesidadApoyo)) {
            if ($necesidadApoyo->getAlumno() === $this) {
                $necesidadApoyo->setAlumno(null);
            }
        }
        return $this;
    }
}