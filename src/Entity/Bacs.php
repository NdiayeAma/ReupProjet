<?php

namespace App\Entity;

use App\Repository\BacsRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BacsRepository::class)]
class Bacs
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\Column]
    private ?int $livres = 0;

    #[ORM\Column]
    private ?int $disponible = 0;

    #[ORM\Column]
    private ?int $commandes = 0;

    #[ORM\Column]
    private ?int $alloues = 0;

    #[ORM\Column(length: 255)]
    private ?string $photobac = null;

    #[ORM\Column]
    private ?int $volume = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;

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

    public function getDisponible(): ?int
    {
        return $this->disponible;
    }

    public function setDisponible(int $disponible): static
    {
        $this->disponible = $disponible;

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

    public function getAlloues(): ?int
    {
        return $this->alloues;
    }

    public function setAlloues(int $alloues): static
    {
        $this->alloues = $alloues;

        return $this;
    }

    public function getPhotobac(): ?string
    {
        return $this->photobac;
    }

    public function setPhotobac(string $photobac): static
    {
        $this->photobac = $photobac;

        return $this;
    }

    public function getVolume(): ?int
    {
        return $this->volume;
    }

    public function setVolume(int $volume): static
    {
        $this->volume = $volume;

        return $this;
    }
}
