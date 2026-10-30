<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\AlergiasRepository;
use Doctrine\ORM\Mapping as ORM;

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

    #[ORM\Column]
    private ?int $alumnoCodigo = null;

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
