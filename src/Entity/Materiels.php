<?php

namespace App\Entity;

use App\Repository\MaterielsRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MaterielsRepository::class)]
class Materiels
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $type = null;

    #[ORM\Column]
    private ?int $besoin = 0;

    #[ORM\Column]
    private ?int $commandes = 0;

    #[ORM\Column]
    private ?int $disponibles = 0;

    #[ORM\Column]
    private ?int $alloues = 0;

    #[ORM\Column(length: 255)]
    private ?string $photo = null;

    #[ORM\Column]
    private ?int $livres = 0;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getBesoin(): ?int
    {
        return $this->besoin;
    }

    public function setBesoin(int $besoin): static
    {
        $this->besoin = $besoin;

        return $this;
    }

    public function getCommandes(): ?int
    {
        return $this->commandes;
    }

    public function setCommandes(int $commandes): static
    {
        $this->commandes = $commandes;

        return $this;
    }

    public function getDisponibles(): ?int
    {
        return $this->disponibles;
    }

    public function setDisponibles(int $disponibles): static
    {
        $this->disponibles = $disponibles;

        return $this;
    }

    public function getAlloues(): ?int
    {
        return $this->alloues;
    }

    public function setAlloues(int $alloues): static
    {
        $this->alloues = $alloues;

        return $this;
    }

    public function getPhoto(): ?string
    {
        return $this->photo;
    }

    public function setPhoto(string $photo): static
    {
        $this->photo = $photo;

        return $this;
    }

    public function getLivres(): ?int
    {
        return $this->livres;
    }

    public function setLivres(int $livres): static
    {
        $this->livres = $livres;

        return $this;
    }
}
