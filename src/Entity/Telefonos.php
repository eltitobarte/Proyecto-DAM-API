<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\TelefonosRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TelefonosRepository::class)]
#[ApiResource]
class Telefonos
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 24)]
    private ?string $telefono = null;

    #[ORM\Column]
    private ?int $idUsuario = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTelefono(): ?string
    {
        return $this->telefono;
    }

    public function setTelefono(string $telefono): static
    {
        $this->telefono = $telefono;

        return $this;
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
}
