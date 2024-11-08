<?php

namespace App\Entity;

use App\Repository\EvenementRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EvenementRepository::class)]
class Evenement
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $datedebut = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $datefin = null;

    /**
     * @var Collection<int, Site>
     */
    #[ORM\ManyToMany(targetEntity: Site::class, inversedBy: 'evenements')]
    private Collection $sites;

    /**
     * @var Collection<int, Hall>
     */
    #[ORM\ManyToMany(targetEntity: Hall::class, inversedBy: 'evenements')]
    private Collection $leshalls;

    /**
     * @var Collection<int, Suivi>
     */
    #[ORM\OneToMany(targetEntity: Suivi::class, mappedBy: 'evenement')]
    private Collection $suivis;

    #[ORM\ManyToOne(inversedBy: 'evenements')]
    private ?Client $leclient = null;

    /**
     * @var Collection<int, Commentaire>
     */
    #[ORM\OneToMany(targetEntity: Commentaire::class, mappedBy: 'evenement')]
    private Collection $commentaires;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $montageDebut = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $montageFin = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $exploitationDebut = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $exploitationFin = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $demontageDebut = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $demontageFin = null;

    /**
     * @var Collection<int, Donation>
     */
    #[ORM\OneToMany(targetEntity: Donation::class, mappedBy: 'evenement')]
    private Collection $donations;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $lien = null;

    public function __construct()
    {
        $this->sites = new ArrayCollection();
        $this->leshalls = new ArrayCollection();
        $this->suivis = new ArrayCollection();
        $this->commentaires = new ArrayCollection();
        $this->donations = new ArrayCollection();
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

    public function getDatedebut(): ?\DateTimeInterface
    {
        return $this->datedebut;
    }

    public function setDatedebut(\DateTimeInterface $datedebut): static
    {
        $this->datedebut = $datedebut;

        return $this;
    }

    public function getDatefin(): ?\DateTimeInterface
    {
        return $this->datefin;
    }

    public function setDatefin(\DateTimeInterface $datefin): static
    {
        $this->datefin = $datefin;

        return $this;
    }

    /**
     * @return Collection<int, Site>
     */
    public function getSites(): Collection
    {
        return $this->sites;
    }

    public function addSite(Site $site): static
    {
        if (!$this->sites->contains($site)) {
            $this->sites->add($site);
        }

        return $this;
    }

    public function removeSite(Site $site): static
    {
        $this->sites->removeElement($site);

        return $this;
    }

    /**
     * @return Collection<int, Hall>
     */
    public function getLeshalls(): Collection
    {
        return $this->leshalls;
    }

    public function addLeshall(Hall $leshall): static
    {
        if (!$this->leshalls->contains($leshall)) {
            $this->leshalls->add($leshall);
        }

        return $this;
    }

    public function removeLeshall(Hall $leshall): static
    {
        $this->leshalls->removeElement($leshall);

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
            $suivi->setEvenement($this);
        }

        return $this;
    }

    public function removeSuivi(Suivi $suivi): static
    {
        if ($this->suivis->removeElement($suivi)) {
            // set the owning side to null (unless already changed)
            if ($suivi->getEvenement() === $this) {
                $suivi->setEvenement(null);
            }
        }

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
    public function __toString(): string
    {
        return $this->nom ;
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
            $commentaire->setEvenement($this);
        }

        return $this;
    }

    public function removeCommentaire(Commentaire $commentaire): static
    {
        if ($this->commentaires->removeElement($commentaire)) {
            // set the owning side to null (unless already changed)
            if ($commentaire->getEvenement() === $this) {
                $commentaire->setEvenement(null);
            }
        }

        return $this;
    }

    public function getMontageDebut(): ?\DateTimeInterface
    {
        return $this->montageDebut;
    }

    public function setMontageDebut(?\DateTimeInterface $montageDebut): static
    {
        $this->montageDebut = $montageDebut;

        return $this;
    }

    public function getMontageFin(): ?\DateTimeInterface
    {
        return $this->montageFin;
    }

    public function setMontageFin(?\DateTimeInterface $montageFin): static
    {
        $this->montageFin = $montageFin;

        return $this;
    }

    public function getExploitationDebut(): ?\DateTimeInterface
    {
        return $this->exploitationDebut;
    }

    public function setExploitationDebut(?\DateTimeInterface $exploitationDebut): static
    {
        $this->exploitationDebut = $exploitationDebut;

        return $this;
    }

    public function getExploitationFin(): ?\DateTimeInterface
    {
        return $this->exploitationFin;
    }

    public function setExploitationFin(?\DateTimeInterface $exploitationFin): static
    {
        $this->exploitationFin = $exploitationFin;

        return $this;
    }

    public function getDemontageDebut(): ?\DateTimeInterface
    {
        return $this->demontageDebut;
    }

    public function setDemontageDebut(?\DateTimeInterface $demontageDebut): static
    {
        $this->demontageDebut = $demontageDebut;

        return $this;
    }

    public function getDemontageFin(): ?\DateTimeInterface
    {
        return $this->demontageFin;
    }

    public function setDemontageFin(?\DateTimeInterface $demontageFin): static
    {
        $this->demontageFin = $demontageFin;

        return $this;
    }

    /**
     * @return Collection<int, Donation>
     */
    public function getDonations(): Collection
    {
        return $this->donations;
    }

    public function addDonation(Donation $donation): static
    {
        if (!$this->donations->contains($donation)) {
            $this->donations->add($donation);
            $donation->setEvenement($this);
        }

        return $this;
    }

    public function removeDonation(Donation $donation): static
    {
        if ($this->donations->removeElement($donation)) {
            // set the owning side to null (unless already changed)
            if ($donation->getEvenement() === $this) {
                $donation->setEvenement(null);
            }
        }

        return $this;
    }

    public function getLien(): ?string
    {
        return $this->lien;
    }

    public function setLien(?string $lien): static
    {
        $this->lien = $lien;

        return $this;
    }
}
