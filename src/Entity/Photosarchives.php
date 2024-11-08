<?php

namespace App\Entity;

use App\Repository\PhotosarchivesRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PhotosarchivesRepository::class)]
class Photosarchives
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;



    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $dateupload = null;

    /**
     * @var Collection<int, Suivi>
     */
    #[ORM\ManyToMany(targetEntity: Suivi::class, inversedBy: 'photosarchives')]
    private Collection $suivis;

    /**
     * @var Collection<int, Commentaire>
     */
    #[ORM\ManyToMany(targetEntity: Commentaire::class, mappedBy: 'photos')]
    private Collection $commentaires;

    /**
     * @var Collection<int, Suivicentredetri>
     */
    #[ORM\ManyToMany(targetEntity: Suivicentredetri::class, mappedBy: 'photos')]
    private Collection $suivicentredetris;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $commentaire = null;

    #[ORM\ManyToOne(inversedBy: 'photosarchives')]
    private ?Centredetri $centredetri = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $titre = null;

    public function __construct()
    {
        $this->suivis = new ArrayCollection();
        $this->commentaires = new ArrayCollection();
        $this->suivicentredetris = new ArrayCollection();
    }

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



    public function getDateupload(): ?\DateTimeInterface
    {
        return $this->dateupload;
    }

    public function setDateupload(\DateTimeInterface $dateupload): static
    {
        $this->dateupload = $dateupload;

        return $this;
    }

    /**
     * @return Collection<int, Suivi>
     */
    public function getSuivis(): Collection
    {
        return $this->suivis;
    }

    public function addSuivi(Suivi $suivi): static
    {
        if (!$this->suivis->contains($suivi)) {
            $this->suivis->add($suivi);
        }

        return $this;
    }

    public function removeSuivi(Suivi $suivi): static
    {
        $this->suivis->removeElement($suivi);

        return $this;
    }

    /**
     * @return Collection<int, Commentaire>
     */
    public function getCommentaires(): Collection
    {
        return $this->commentaires;
    }

    public function addCommentaire(Commentaire $commentaire): static
    {
        if (!$this->commentaires->contains($commentaire)) {
            $this->commentaires->add($commentaire);
            $commentaire->addPhoto($this);
        }

        return $this;
    }

    public function removeCommentaire(Commentaire $commentaire): static
    {
        if ($this->commentaires->removeElement($commentaire)) {
            $commentaire->removePhoto($this);
        }

        return $this;
    }
    public function __toString(): string
    {
        return $this->nom;
    }

    /**
     * @return Collection<int, Suivicentredetri>
     */
    public function getSuivicentredetris(): Collection
    {
        return $this->suivicentredetris;
    }

    public function addSuivicentredetri(Suivicentredetri $suivicentredetri): static
    {
        if (!$this->suivicentredetris->contains($suivicentredetri)) {
            $this->suivicentredetris->add($suivicentredetri);
            $suivicentredetri->addPhoto($this);
        }

        return $this;
    }

    public function removeSuivicentredetri(Suivicentredetri $suivicentredetri): static
    {
        if ($this->suivicentredetris->removeElement($suivicentredetri)) {
            $suivicentredetri->removePhoto($this);
        }

        return $this;
    }

    public function getCommentaire(): ?string
    {
        return $this->commentaire;
    }

    public function setCommentaire(?string $commentaire): static
    {
        $this->commentaire = $commentaire;

        return $this;
    }

    public function getCentredetri(): ?Centredetri
    {
        return $this->centredetri;
    }

    public function setCentredetri(?Centredetri $centredetri): static
    {
        $this->centredetri = $centredetri;

        return $this;
    }

    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre(?string $titre): static
    {
        $this->titre = $titre;

        return $this;
    }
}
