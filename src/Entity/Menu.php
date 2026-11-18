<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\MenuRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\DBAL\Types\Types;

#[ORM\Entity(repositoryClass: MenuRepository::class)]
#[ApiResource]
class Menu
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $menu = null;

    /**
     * @var Collection<int, Alergenos>
     */
    #[ORM\OneToMany(targetEntity: Alergenos::class, mappedBy: 'menu', cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $alergenos;

    public function __construct()
    {
        $this->alergenos = new ArrayCollection();
    }

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

    /** @return Collection<int, Alergenos> */
    public function getAlergenos(): Collection
    {
        return $this->alergenos;
    }

    public function addAlergeno(Alergenos $alergeno): static
    {
        if (!$this->alergenos->contains($alergeno)) {
            $this->alergenos->add($alergeno);
            $alergeno->setMenu($this);
        }
        return $this;
    }

    public function removeAlergeno(Alergenos $alergeno): static
    {
        if ($this->alergenos->removeElement($alergeno)) {
            if ($alergeno->getMenu() === $this) {
                $alergeno->setMenu(null);
            }
        }
        return $this;
    }
}