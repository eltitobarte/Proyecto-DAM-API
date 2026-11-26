<?php

namespace App\Entity;

use App\Repository\EventosRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: EventosRepository::class)]
#[ORM\Table(name: 'eventos')]
class Eventos
{
    public const TIPO_GENERAL = 'general';
    public const TIPO_REUNION = 'reunion';
    public const TIPO_EXCURSION = 'excursion';
    public const TIPO_FESTIVO = 'festivo';
    public const TIPO_AVISO = 'aviso';

    public const TIPOS = [
        self::TIPO_GENERAL,
        self::TIPO_REUNION,
        self::TIPO_EXCURSION,
        self::TIPO_FESTIVO,
        self::TIPO_AVISO,
    ];

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'El título del evento es obligatorio.')]
    #[Assert\Length(max: 255, maxMessage: 'El título no puede superar los {{ limit }} caracteres.')]
    private ?string $titulo = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $descripcion = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    #[Assert\NotNull(message: 'La fecha de inicio es obligatoria.')]
    private ?\DateTimeImmutable $inicio = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    #[Assert\NotNull(message: 'La fecha de fin es obligatoria.')]
    #[Assert\GreaterThan(propertyPath: 'inicio', message: 'La fecha de fin debe ser posterior a la fecha de inicio.')]
    private ?\DateTimeImmutable $fin = null;

    #[ORM\Column]
    private bool $todoElDia = false;

    #[ORM\Column(length: 50)]
    #[Assert\Choice(choices: self::TIPOS, message: 'El tipo de evento no es válido.')]
    private string $tipo = self::TIPO_GENERAL;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\Length(max: 255, maxMessage: 'El lugar no puede superar los {{ limit }} caracteres.')]
    private ?string $lugar = null;

    #[ORM\ManyToOne(targetEntity: Aula::class)]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private ?Aula $aula = null;

    #[ORM\ManyToOne(targetEntity: Usuarios::class)]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private ?Usuarios $creador = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private ?\DateTimeImmutable $fechaCreacion = null;

    public function __construct()
    {
        $this->fechaCreacion = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitulo(): ?string
    {
        return $this->titulo;
    }

    public function setTitulo(string $titulo): static
    {
        $this->titulo = $titulo;
        return $this;
    }

    public function getDescripcion(): ?string
    {
        return $this->descripcion;
    }

    public function setDescripcion(?string $descripcion): static
    {
        $this->descripcion = $descripcion;
        return $this;
    }

    public function getInicio(): ?\DateTimeImmutable
    {
        return $this->inicio;
    }

    public function setInicio(\DateTimeImmutable $inicio): static
    {
        $this->inicio = $inicio;
        return $this;
    }

    public function getFin(): ?\DateTimeImmutable
    {
        return $this->fin;
    }

    public function setFin(\DateTimeImmutable $fin): static
    {
        $this->fin = $fin;
        return $this;
    }

    public function isTodoElDia(): bool
    {
        return $this->todoElDia;
    }

    public function setTodoElDia(bool $todoElDia): static
    {
        $this->todoElDia = $todoElDia;
        return $this;
    }

    public function getTipo(): string
    {
        return $this->tipo;
    }

    public function setTipo(string $tipo): static
    {
        $this->tipo = $tipo;
        return $this;
    }

    public function getLugar(): ?string
    {
        return $this->lugar;
    }

    public function setLugar(?string $lugar): static
    {
        $this->lugar = $lugar;
        return $this;
    }

    public function getAula(): ?Aula
    {
        return $this->aula;
    }

    public function setAula(?Aula $aula): static
    {
        $this->aula = $aula;
        return $this;
    }

    public function getCreador(): ?Usuarios
    {
        return $this->creador;
    }

    public function setCreador(?Usuarios $creador): static
    {
        $this->creador = $creador;
        return $this;
    }

    public function getFechaCreacion(): ?\DateTimeImmutable
    {
        return $this->fechaCreacion;
    }

    public function setFechaCreacion(\DateTimeImmutable $fechaCreacion): static
    {
        $this->fechaCreacion = $fechaCreacion;
        return $this;
    }
}
