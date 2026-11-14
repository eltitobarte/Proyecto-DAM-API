<?php

namespace App\Entity;

use App\Repository\MenuRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MenuRepository::class)]
class Menu
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $menu = null;

    #[ORM\Column]
    private ?int $comidaCodigo = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMenu(): ?string
    {
        return $this->menu;
    }

    public function setMenu(string $menu): static
    {
        $this->menu = $menu;

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
