<?php

namespace App\Entity;

use App\Repository\UsuariosRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UsuariosRepository::class)]
#[ORM\InheritanceType('JOINED')]
#[ORM\DiscriminatorColumn(name: 'rol', type: 'string', length: 30)]
#[ORM\DiscriminatorMap([
    'administrador'  => Administrador::class,
    'administracion' => Administracion::class,
    'tutor_legal'    => TutorLegal::class,
    'profesor'       => Profesor::class,
])]
abstract class Usuarios
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    protected ?int $id = null;

    #[ORM\Column(length: 8)]
    protected ?string $contrasenya = null;

    #[ORM\Column(length: 255)]
    protected ?string $correoElectronico = null;

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
}