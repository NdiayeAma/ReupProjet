<?php

namespace App\Entity;

use App\Repository\CommentaireRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CommentaireRepository::class)]
class Commentaire
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $libelle = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $datesoumission = null;

    /**
     * @var Collection<int, Photosarchives>
     */
    #[ORM\ManyToMany(targetEntity: Photosarchives::class, inversedBy: 'commentaires')]
    private Collection $photos;

    #[ORM\ManyToOne(inversedBy: 'commentaires')]
    private ?Evenement $evenement = null;

    public function __construct()
    {
        $this->photos = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLibelle(): ?string
    {
        return $this->libelle;
    }

    public function setLibelle(string $libelle): static
    {
        $this->libelle = $libelle;

        return $this;
    }

    public function getDatesoumission(): ?\DateTimeInterface
    {
        return $this->datesoumission;
    }

    public function setDatesoumission(\DateTimeInterface $datesoumission): static
    {
        $this->datesoumission = $datesoumission;

        return $this;
    }

    /**
     * @return Collection<int, Photosarchives>
     */
    public function getPhotos(): Collection
    {
        return $this->photos;
    }

    public function addPhoto(Photosarchives $photo): static
    {
        if (!$this->photos->contains($photo)) {
            $this->photos->add($photo);
        }

        return $this;
    }

    public function removePhoto(Photosarchives $photo): static
    {
        $this->photos->removeElement($photo);

        return $this;
    }

    public function getEvenement(): ?Evenement
    {
        return $this->evenement;
    }

    public function setEvenement(?Evenement $evenement): static
    {
        $this->evenement = $evenement;

        return $this;
    }
    public function __toString(): string
    {
        // Concaténer le nom avec une autre variable
        return $this->libelle;
    }
}
