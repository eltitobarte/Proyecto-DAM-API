<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\NecesidadesApoyoEdRepository;
use Doctrine\ORM\Mapping as ORM;

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

    #[ORM\Column]
    private ?int $alumnoCodigo = null;

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

    public function getAlumnoCodigo(): ?int
    {
        return $this->alumnoCodigo;
    }

    public function setAlumnoCodigo(int $alumnoCodigo): static
    {
        $this->alumnoCodigo = $alumnoCodigo;

        return $this;
    }
}
