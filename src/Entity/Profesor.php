<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use App\State\UserPasswordHasherProcessor;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;

#[ORM\Entity(repositoryClass: ProfesorRepository::class)] // (o el repositorio correspondiente)
#[ApiResource(operations: [
    new GetCollection(),
    new Get(),
    new Post(processor: UserPasswordHasherProcessor::class),
    new Put(processor: UserPasswordHasherProcessor::class),
    new Patch(processor: UserPasswordHasherProcessor::class),
    new Delete(),
])]
class Profesor extends Usuarios
{
    #[ORM\Column(length: 255)]
    private ?string $nombre = null;

    #[ORM\Column(length: 255)]
    private ?string $apellidos = null;

    #[ORM\ManyToOne(inversedBy: 'profesores')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Aula $imparte = null;

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

    public function getImparte(): ?Aula
    {
        return $this->imparte;
    }

    public function setImparte(?Aula $imparte): static
    {
        $this->imparte = $imparte;
        return $this;
    }
}