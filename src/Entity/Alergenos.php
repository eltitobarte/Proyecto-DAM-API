<?php

namespace App\Entity;

use App\Repository\AlergenosRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AlergenosRepository::class)]
class Alergenos
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $alergeno = null;

    #[ORM\Column]
    private ?int $comidaCodigo = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAlergeno(): ?string
    {
        return $this->alergeno;
    }

    public function setAlergeno(string $alergeno): static
    {
        $this->alergeno = $alergeno;

        return $this;
    }

    public function getComidaCodigo(): ?int
    {
        return $this->comidaCodigo;
    }

    public function setComidaCodigo(int $comidaCodigo): static
    {
        $this->comidaCodigo = $comidaCodigo;

        return $this;
    }
}
