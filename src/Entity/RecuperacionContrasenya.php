<?php

namespace App\Entity;

use App\Repository\RecuperacionContrasenyaRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RecuperacionContrasenyaRepository::class)]
#[ORM\Table(name: 'recuperacion_contrasenya')]
class RecuperacionContrasenya
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Usuarios::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?Usuarios $usuario = null;

    #[ORM\Column(length: 64, unique: true)]
    private ?string $tokenHash = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private ?\DateTimeImmutable $fechaSolicitud = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private ?\DateTimeImmutable $fechaExpiracion = null;

    #[ORM\Column]
    private bool $usado = false;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsuario(): ?Usuarios
    {
        return $this->usuario;
    }

    public function setUsuario(?Usuarios $usuario): static
    {
        $this->usuario = $usuario;
        return $this;
    }

    public function getTokenHash(): ?string
    {
        return $this->tokenHash;
    }

    public function setTokenHash(string $tokenHash): static
    {
        $this->tokenHash = $tokenHash;
        return $this;
    }

    public function getFechaSolicitud(): ?\DateTimeImmutable
    {
        return $this->fechaSolicitud;
    }

    public function setFechaSolicitud(\DateTimeImmutable $fechaSolicitud): static
    {
        $this->fechaSolicitud = $fechaSolicitud;
        return $this;
    }

    public function getFechaExpiracion(): ?\DateTimeImmutable
    {
        return $this->fechaExpiracion;
    }

    public function setFechaExpiracion(\DateTimeImmutable $fechaExpiracion): static
    {
        $this->fechaExpiracion = $fechaExpiracion;
        return $this;
    }

    public function isUsado(): bool
    {
        return $this->usado;
    }

    public function setUsado(bool $usado): static
    {
        $this->usado = $usado;
        return $this;
    }

    public function isVigente(): bool
    {
        return !$this->usado
            && $this->fechaExpiracion !== null
            && $this->fechaExpiracion > new \DateTimeImmutable();
    }
}
