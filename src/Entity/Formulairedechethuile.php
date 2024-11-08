<?php

namespace App\Entity;

use App\Repository\FormulairedechethuileRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FormulairedechethuileRepository::class)]
class Formulairedechethuile
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $nomhall = null;

    #[ORM\Column(length: 255)]
    private ?string $allee = null;

    #[ORM\Column]
    private ?int $nombredesacs = null;

    #[ORM\Column(length: 255)]
    private ?string $quantitesacs = null;

    #[ORM\Column]
    private ?int $nombredebidons = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $commentaire = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomhall(): ?int
    {
        return $this->nomhall;
    }

    public function setNomhall(int $nomhall): static
    {
        $this->nomhall = $nomhall;

        return $this;
    }

    public function getAllee(): ?string
    {
        return $this->allee;
    }

    public function setAllee(string $allee): static
    {
        $this->allee = $allee;

        return $this;
    }

    public function getNombredesacs(): ?int
    {
        return $this->nombredesacs;
    }

    public function setNombredesacs(int $nombredesacs): static
    {
        $this->nombredesacs = $nombredesacs;

        return $this;
    }

    public function getQuantitesacs(): ?string
    {
        return $this->quantitesacs;
    }

    public function setQuantitesacs(string $quantitesacs): static
    {
        $this->quantitesacs = $quantitesacs;

        return $this;
    }

    public function getNombredebidons(): ?int
    {
        return $this->nombredebidons;
    }

    public function setNombredebidons(int $nombredebidons): static
    {
        $this->nombredebidons = $nombredebidons;

        return $this;
    }

    public function getCommentaire(): ?string
    {
        return $this->commentaire;
    }

    public function setCommentaire(string $commentaire): static
    {
        $this->commentaire = $commentaire;

        return $this;
    }
}
