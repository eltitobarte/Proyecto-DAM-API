<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\ChatRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ChatRepository::class)]
#[ApiResource]
class Chat
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $idUsuario = null;

    #[ORM\Column(length: 255)]
    private ?string $nombreUsuario = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $mensaje = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $fechaEnvio = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdUsuario(): ?int
    {
        return $this->idUsuario;
    }

    public function setIdUsuario(int $idUsuario): static
    {
        $this->idUsuario = $idUsuario;

        return $this;
    }

    public function getNombreUsuario(): ?string
    {
        return $this->nombreUsuario;
    }

    public function setNombreUsuario(string $nombreUsuario): static
    {
        $this->nombreUsuario = $nombreUsuario;

        return $this;
    }

    public function getMensaje(): ?string
    {
        return $this->mensaje;
    }

    public function setMensaje(string $mensaje): static
    {
        $this->mensaje = $mensaje;

        return $this;
    }

    public function getFechaEnvio(): ?\DateTimeImmutable
    {
        return $this->fechaEnvio;
    }

    public function setFechaEnvio(\DateTimeImmutable $fechaEnvio): static
    {
        $this->fechaEnvio = $fechaEnvio;

        return $this;
    }
}
