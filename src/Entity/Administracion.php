<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\AdministracionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AdministracionRepository::class)]
#[ApiResource]
class Administracion
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nombre = null;

    #[ORM\Column(length: 255)]
    private ?string $apellidos = null;

    /**
     * @var Collection<int, Altas>
     */
    #[ORM\OneToMany(targetEntity: Altas::class, mappedBy: 'idAdmin', orphanRemoval: true)]
    private Collection $gestionAltas;

    /**
     * @var Collection<int, Bajas>
     */
    #[ORM\OneToMany(targetEntity: Bajas::class, mappedBy: 'idAdmin', orphanRemoval: true)]
    private Collection $gestionBajas;

    public function __construct()
    {
        $this->gestionAltas = new ArrayCollection();
        $this->gestionBajas = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

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

    /**
     * @return Collection<int, Altas>
     */
    public function getGestionAltas(): Collection
    {
        return $this->gestionAltas;
    }

    public function addGestionAlta(Altas $gestionAlta): static
    {
        if (!$this->gestionAltas->contains($gestionAlta)) {
            $this->gestionAltas->add($gestionAlta);
            $gestionAlta->setIdAdmin($this);
        }

        return $this;
    }

    public function removeGestionAlta(Altas $gestionAlta): static
    {
        if ($this->gestionAltas->removeElement($gestionAlta)) {
            // set the owning side to null (unless already changed)
            if ($gestionAlta->getIdAdmin() === $this) {
                $gestionAlta->setIdAdmin(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Bajas>
     */
    public function getGestionBajas(): Collection
    {
        return $this->gestionBajas;
    }

    public function addGestionBaja(Bajas $gestionBaja): static
    {
        if (!$this->gestionBajas->contains($gestionBaja)) {
            $this->gestionBajas->add($gestionBaja);
            $gestionBaja->setIdAdmin($this);
        }

        return $this;
    }

    public function removeGestionBaja(Bajas $gestionBaja): static
    {
        if ($this->gestionBajas->removeElement($gestionBaja)) {
            // set the owning side to null (unless already changed)
            if ($gestionBaja->getIdAdmin() === $this) {
                $gestionBaja->setIdAdmin(null);
            }
        }

        return $this;
    }
}
