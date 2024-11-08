<?php

namespace App\Entity;

use App\Repository\SuiviRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SuiviRepository::class)]
class Suivi
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $flux = null;

    #[ORM\Column(length: 255)]
    private ?string $typecontenant = null;

    #[ORM\Column]
    private ?int $volumecontenant = null;

    #[ORM\Column]
    private ?float $poids = null;

    #[ORM\Column]
    private ?int $tauxderemplissage = null;

    #[ORM\Column]
    private ?int $collecte = null;

    #[ORM\Column]
    private ?int $cumulflux = null;

    #[ORM\Column(length: 255)]
    private ?string $qualitedetribennes = null;

    #[ORM\Column]
    private ?int $estimatifbennes = null;

    #[ORM\Column(length: 255)]
    private ?string $collectebennes = null;

    #[ORM\Column]
    private ?int $cumulbennes = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $datedesoumission = null;

    #[ORM\ManyToOne(inversedBy: 'lessuivis')]
    private ?Hall $hall = null;

    #[ORM\ManyToOne(inversedBy: 'suivis')]
    private ?Client $leclient = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $commentaire = null;

    #[ORM\ManyToOne(inversedBy: 'suivis')]
    private ?Evenement $evenement = null;

    /**
     * @var Collection<int, Photosarchives>
     */
    #[ORM\ManyToMany(targetEntity: Photosarchives::class, mappedBy: 'suivis')]
    private Collection $photosarchives;

    #[ORM\ManyToOne(inversedBy: 'suivis')]
    private ?Centredetri $centredetris = null;

    #[ORM\Column(type: Types::TIME_MUTABLE)]
    private ?\DateTimeInterface $heure = null;

    #[ORM\ManyToOne(inversedBy: 'suivis')]
    private ?User $auteur = null;
    
     #[ORM\Column(length: 255, nullable: true)]
    private ?string $exutoire = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $numerobordereau = null;


    public function __construct()
    {
        $this->photosarchives = new ArrayCollection();
    }



    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFlux(): ?string
    {
        return $this->flux;
    }

    public function setFlux(string $flux): static
    {
        $this->flux = $flux;

        return $this;
    }

    public function getTypecontenant(): ?string
    {
        return $this->typecontenant;
    }

    public function setTypecontenant(string $typecontenant): static
    {
        $this->typecontenant = $typecontenant;

        return $this;
    }

    public function getVolumecontenant(): ?int
    {
        return $this->volumecontenant;
    }

    public function setVolumecontenant(int $volumecontenant): static
    {
        $this->volumecontenant = $volumecontenant;

        return $this;
    }

    public function getPoids(): ?float
    {
        return $this->poids;
    }

    public function setPoids(float $poids): static
    {
        $this->poids = $poids;

        return $this;
    }

    public function getTauxderemplissage(): ?int
    {
        return $this->tauxderemplissage;
    }

    public function setTauxderemplissage(int $tauxderemplissage): static
    {
        $this->tauxderemplissage = $tauxderemplissage;

        return $this;
    }

    public function getCollecte(): ?int
    {
        return $this->collecte;
    }

    public function setCollecte(int $collecte): static
    {
        $this->collecte = $collecte;

        return $this;
    }

    public function getCumulflux(): ?int
    {
        return $this->cumulflux;
    }

    public function setCumulflux(int $cumulflux): static
    {
        $this->cumulflux = $cumulflux;

        return $this;
    }

    public function getQualitedetribennes(): ?string
    {
        return $this->qualitedetribennes;
    }

    public function setQualitedetribennes(string $qualitedetribennes): static
    {
        $this->qualitedetribennes = $qualitedetribennes;

        return $this;
    }

    public function getEstimatifbennes(): ?int
    {
        return $this->estimatifbennes;
    }

    public function setEstimatifbennes(int $estimatifbennes): static
    {
        $this->estimatifbennes = $estimatifbennes;

        return $this;
    }

    public function getCollectebennes(): ?string
    {
        return $this->collectebennes;
    }

    public function setCollectebennes(string $collectebennes): static
    {
        $this->collectebennes = $collectebennes;

        return $this;
    }

    public function getCumulbennes(): ?int
    {
        return $this->cumulbennes;
    }

    public function setCumulbennes(int $cumulbennes): static
    {
        $this->cumulbennes = $cumulbennes;

        return $this;
    }

    public function getDatedesoumission(): ?\DateTimeInterface
    {
        return $this->datedesoumission;
    }

    public function setDatedesoumission(\DateTimeInterface $datedesoumission): static
    {
        $this->datedesoumission = $datedesoumission;

        return $this;
    }

    public function getHall(): ?Hall
    {
        return $this->hall;
    }

    public function setHall(?Hall $hall): static
    {
        $this->hall = $hall;

        return $this;
    }

    public function getLeclient(): ?Client
    {
        return $this->leclient;
    }

    public function setLeclient(?Client $leclient): static
    {
        $this->leclient = $leclient;

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

    public function getEvenement(): ?Evenement
    {
        return $this->evenement;
    }

    public function setEvenement(?Evenement $evenement): static
    {
        $this->evenement = $evenement;

        return $this;
    }

    /**
     * @return Collection<int, Photosarchives>
     */
    public function getPhotosarchives(): Collection
    {
        return $this->photosarchives;
    }

    public function addPhotosarchive(Photosarchives $photosarchive): static
    {
        if (!$this->photosarchives->contains($photosarchive)) {
            $this->photosarchives->add($photosarchive);
            $photosarchive->addSuivi($this);
        }

        return $this;
    }

    public function removePhotosarchive(Photosarchives $photosarchive): static
    {
        if ($this->photosarchives->removeElement($photosarchive)) {
            $photosarchive->removeSuivi($this);
        }

        return $this;
    }
    public function __toString(): string
    {
        // Concaténer le nom avec une autre variable
        return $this->flux;
    }

    public function getCentredetris(): ?Centredetri
    {
        return $this->centredetris;
    }

    public function setCentredetris(?Centredetri $centredetris): static
    {
        $this->centredetris = $centredetris;

        return $this;
    }

    public function getHeure(): ?\DateTimeInterface
    {
        return $this->heure;
    }

    public function setHeure(\DateTimeInterface $heure): static
    {
        $this->heure = $heure;

        return $this;
    }

    public function getAuteur(): ?User
    {
        return $this->auteur;
    }

    public function setAuteur(?User $auteur): static
    {
        $this->auteur = $auteur;

        return $this;
    }
      public function getExutoire(): ?string
    {
        return $this->exutoire;
    }

    public function setExutoire(string $exutoire): static
    {
        $this->exutoire = $exutoire;

        return $this;
    }

    public function getNumerobordereau(): ?string
    {
        return $this->numerobordereau;
    }

    public function setNumerobordereau(?string $numerobordereau): static
    {
        $this->numerobordereau = $numerobordereau;

        return $this;
    }




}
