<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\ChatRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;


#[ORM\Entity(repositoryClass: ChatRepository::class)]
#[ApiResource]
class Chat
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Usuarios $remitente = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Usuarios $destinatario = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $mensaje = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $fechaEnvio = null;

    public function __construct()
    {
        $this->fechaEnvio = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRemitente(): ?Usuarios
    {
        return $this->remitente;
    }

    public function setRemitente(?Usuarios $remitente): static
    {
        $this->remitente = $remitente;
        return $this;
    }

    public function getDestinatario(): ?Usuarios
    {
        return $this->destinatario;
    }

    public function setDestinatario(?Usuarios $destinatario): static
    {
        $this->destinatario = $destinatario;
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