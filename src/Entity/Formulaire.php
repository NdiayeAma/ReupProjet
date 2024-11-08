<?php

namespace App\Entity;

use App\Repository\FormulaireRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FormulaireRepository::class)]
class Formulaire
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $nomcompagnie = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $hall = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $alleenumerostand = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $contactstandiste = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $rse = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $donnerdesmateriaux = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $donnerbois = null;



    #[ORM\Column(type: Types::TEXT)]
    private ?string $quantitefourniture = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $quantiteautresmateriaux = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $commentaire = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomcompagnie(): ?string
    {
        return $this->nomcompagnie;
    }

    public function setNomcompagnie(string $nomcompagnie): static
    {
        $this->nomcompagnie = $nomcompagnie;

        return $this;
    }

    public function getHall(): ?string
    {
        return $this->hall;
    }

    public function setHall(string $hall): static
    {
        $this->hall = $hall;

        return $this;
    }

    public function getAlleenumerostand(): ?string
    {
        return $this->alleenumerostand;
    }

    public function setAlleenumerostand(string $alleenumerostand): static
    {
        $this->alleenumerostand = $alleenumerostand;

        return $this;
    }

    public function getContactstandiste(): ?string
    {
        return $this->contactstandiste;
    }

    public function setContactstandiste(string $contactstandiste): static
    {
        $this->contactstandiste = $contactstandiste;

        return $this;
    }

    public function getRse(): ?string
    {
        return $this->rse;
    }

    public function setRse(string $rse): static
    {
        $this->rse = $rse;

        return $this;
    }

    public function getDonnerdesmateriaux(): ?string
    {
        return $this->donnerdesmateriaux;
    }

    public function setDonnerdesmateriaux(string $donnerdesmateriaux): static
    {
        $this->donnerdesmateriaux = $donnerdesmateriaux;

        return $this;
    }

    public function getDonnerbois(): ?string
    {
        return $this->donnerbois;
    }

    public function setDonnerbois(string $donnerbois): static
    {
        $this->donnerbois = $donnerbois;

        return $this;
    }


    public function getQuantitefourniture(): ?string
    {
        return $this->quantitefourniture;
    }

    public function setQuantitefourniture(string $quantitefourniture): static
    {
        $this->quantitefourniture = $quantitefourniture;

        return $this;
    }

    public function getQuantiteautresmateriaux(): ?string
    {
        return $this->quantiteautresmateriaux;
    }

    public function setQuantiteautresmateriaux(string $quantiteautresmateriaux): static
    {
        $this->quantiteautresmateriaux = $quantiteautresmateriaux;

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
