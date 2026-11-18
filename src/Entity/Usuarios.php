<?php

namespace App\Entity;

use App\Repository\UsuariosRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\DBAL\Types\Types;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UsuariosRepository::class)]
#[ORM\InheritanceType('JOINED')]
#[ORM\DiscriminatorColumn(name: 'rol', type: 'string', length: 30)]
#[ORM\DiscriminatorMap([
    'administrador'  => Administrador::class,
    'administracion' => Administracion::class,
    'tutor_legal'    => TutorLegal::class,
    'profesor'       => Profesor::class,
])]
abstract class Usuarios implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    protected ?int $id = null;

    #[ORM\Column(length: 255)]
    protected ?string $contrasenya = null;

    #[ORM\Column(length: 255, unique: true)]
    protected ?string $correoElectronico = null;

    /**
     * @var Collection<int, Telefonos>
     */
    #[ORM\OneToMany(targetEntity: Telefonos::class, mappedBy: 'usuario', cascade: ['persist', 'remove'], orphanRemoval: true)]
    protected Collection $telefonos;

    public function __construct()
    {
        $this->telefonos = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getContrasenya(): ?string
    {
        return $this->contrasenya;
    }

    public function setContrasenya(string $contrasenya): static
    {
        $this->contrasenya = $contrasenya;
        return $this;
    }

    public function getCorreoElectronico(): ?string
    {
        return $this->correoElectronico;
    }

    public function setCorreoElectronico(string $correoElectronico): static
    {
        $this->correoElectronico = $correoElectronico;
        return $this;
    }

    /**
     * @return Collection<int, Telefonos>
     */
    public function getTelefonos(): Collection
    {
        return $this->telefonos;
    }

    public function addTelefono(Telefonos $telefono): static
    {
        if (!$this->telefonos->contains($telefono)) {
            $this->telefonos->add($telefono);
            $telefono->setUsuario($this);
        }
        return $this;
    }

    public function removeTelefono(Telefonos $telefono): static
    {
        if ($this->telefonos->removeElement($telefono)) {
            if ($telefono->getUsuario() === $this) {
                $telefono->setUsuario(null);
            }
        }
        return $this;
    }

    // --- Métodos obligatorios de Symfony Security ---

    public function getUserIdentifier(): string
    {
        return (string) $this->correoElectronico;
    }

    public function getPassword(): ?string
    {
        return $this->contrasenya;
    }

    public function getRoles(): array
    {
        // Asigna automáticamente el rol según la subclase instanciada
        $rol = match (true) {
            $this instanceof Administrador => 'ROLE_ADMINISTRADOR',
            $this instanceof Administracion => 'ROLE_ADMINISTRACION',
            $this instanceof Profesor => 'ROLE_PROFESOR',
            $this instanceof TutorLegal => 'ROLE_TUTOR_LEGAL',
            default => 'ROLE_USER',
        };

        return [$rol, 'ROLE_USER'];
    }

    public function eraseCredentials(): void
    {
        // Si guardaras datos temporales sensibles, se limpiarían aquí
    }
}