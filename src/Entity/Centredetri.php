<?php

namespace App\Entity;

use App\Repository\CentredetriRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CentredetriRepository::class)]
class Centredetri
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\Column]
    private ?float $taille = null;

    #[ORM\Column]
    private ?int $Barrierages = 0;

    #[ORM\Column]
    private ?int $marquageausol = 0;

    #[ORM\Column]
    private ?int $tonnelle = 0;

    #[ORM\Column]
    private ?int $signaletiques = 0;

    #[ORM\Column]
    private ?int $supportsachs = 0;

    #[ORM\Column]
    private ?int $balances = 0;

    #[ORM\Column]
    private ?int $compacteur = 0;

    #[ORM\Column]
    private ?int $tabledetri = 0;

    #[ORM\Column]
    private ?int $vidoirliquide = 0;

    #[ORM\Column]
    private ?int $troussedesecours = 0;

    #[ORM\Column]
    private ?int $epi = 0;

    #[ORM\Column]
    private ?int $materielsdenettoyage = 0;

    /**
     * @var Collection<int, Hall>
     */
    #[ORM\OneToMany(targetEntity: Hall::class, mappedBy: 'centredetri')]
    private Collection $halls;

    #[ORM\Column(length: 255)]
    private ?string $phase = null;

    #[ORM\Column]
    private ?int $bacdr660 = 0;

    #[ORM\Column]
    private ?int $bacdr240 = 0;

    #[ORM\Column]
    private ?int $bacrecycle240 = 0;

    #[ORM\Column]
    private ?int $bacrecycle660 = 0;

    #[ORM\Column]
    private ?int $bacbiodechet120 = 0;
    #[ORM\Column]
    private ?int $bennebois = 0;

    #[ORM\Column]
    private ?int $bennecarton = 0;

    #[ORM\Column]
    private ?int $benneplastiques = 0;

    /**
     * @var Collection<int, Suivicentredetri>
     */
    #[ORM\OneToMany(targetEntity: Suivicentredetri::class, mappedBy: 'centredetri')]
    private Collection $suivicentredetris;

    #[ORM\Column]
    private ?int $bachenoires = 0;

    /**
     * @var Collection<int, Suivi>
     */
    #[ORM\OneToMany(targetEntity: Suivi::class, mappedBy: 'centredetris')]
    private Collection $suivis;

    #[ORM\ManyToOne(inversedBy: 'centredetris')]
    private ?Site $site = null;

    /**
     * @var Collection<int, Photosarchives>
     */
    #[ORM\OneToMany(targetEntity: Photosarchives::class, mappedBy: 'centredetri')]
    private Collection $photosarchives;

    public function __construct()
    {
        $this->halls = new ArrayCollection();
        $this->suivicentredetris = new ArrayCollection();
        $this->suivis = new ArrayCollection();
        $this->photosarchives = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }
    public function __toString(): string
    {
        return $this->nom ;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }

    public function getTaille(): ?float
    {
        return $this->taille;
    }

    public function setTaille(float $taille): static
    {
        $this->taille = $taille;

        return $this;
    }

    public function getBarrierages(): ?int
    {
        return $this->Barrierages;
    }

    public function setBarrierages(int $Barrierages): static
    {
        $this->Barrierages = $Barrierages;

        return $this;
    }

    public function getMarquageausol(): ?int
    {
        return $this->marquageausol;
    }

    public function setMarquageausol(int $marquageausol): static
    {
        $this->marquageausol = $marquageausol;

        return $this;
    }

    public function getTonnelle(): ?int
    {
        return $this->tonnelle;
    }

    public function setTonnelle(int $tonnelle): static
    {
        $this->tonnelle = $tonnelle;

        return $this;
    }

    public function getSignaletiques(): ?int
    {
        return $this->signaletiques;
    }

    public function setSignaletiques(int $signaletiques): static
    {
        $this->signaletiques = $signaletiques;

        return $this;
    }

    public function getSupportsachs(): ?int
    {
        return $this->supportsachs;
    }

    public function setSupportsachs(int $supportsachs): static
    {
        $this->supportsachs = $supportsachs;

        return $this;
    }

    public function getBalances(): ?int
    {
        return $this->balances;
    }

    public function setBalances(int $balances): static
    {
        $this->balances = $balances;

        return $this;
    }

    public function getCompacteur(): ?int
    {
        return $this->compacteur;
    }

    public function setCompacteur(int $compacteur): static
    {
        $this->compacteur = $compacteur;

        return $this;
    }

    public function getTabledetri(): ?int
    {
        return $this->tabledetri;
    }

    public function setTabledetri(int $tabledetri): static
    {
        $this->tabledetri = $tabledetri;

        return $this;
    }

    public function getVidoirliquide(): ?int
    {
        return $this->vidoirliquide;
    }

    public function setVidoirliquide(int $vidoirliquide): static
    {
        $this->vidoirliquide = $vidoirliquide;

        return $this;
    }

    public function getTroussedesecours(): ?int
    {
        return $this->troussedesecours;
    }

    public function setTroussedesecours(int $troussedesecours): static
    {
        $this->troussedesecours = $troussedesecours;

        return $this;
    }

    public function getEpi(): ?int
    {
        return $this->epi;
    }

    public function setEpi(int $epi): static
    {
        $this->epi = $epi;

        return $this;
    }

    public function getMaterielsdenettoyage(): ?int
    {
        return $this->materielsdenettoyage;
    }

    public function setMaterielsdenettoyage(int $materielsdenettoyage): static
    {
        $this->materielsdenettoyage = $materielsdenettoyage;

        return $this;
    }

    /**
     * @return Collection<int, Hall>
     */
    public function getHalls(): Collection
    {
        return $this->halls;
    }

    public function addHall(Hall $hall): static
    {
        if (!$this->halls->contains($hall)) {
            $this->halls->add($hall);
            $hall->setCentredetri($this);
        }

        return $this;
    }

    public function removeHall(Hall $hall): static
    {
        if ($this->halls->removeElement($hall)) {
            // set the owning side to null (unless already changed)
            if ($hall->getCentredetri() === $this) {
                $hall->setCentredetri(null);
            }
        }

        return $this;
    }

    public function getPhase(): ?string
    {
        return $this->phase;
    }

    public function setPhase(string $phase): static
    {
        $this->phase = $phase;

        return $this;
    }

    public function getBacdr660(): ?int
    {
        return $this->bacdr660;
    }

    public function setBacdr660(int $bacdr660): static
    {
        $this->bacdr660 = $bacdr660;

        return $this;
    }

    public function getBacdr240(): ?int
    {
        return $this->bacdr240;
    }

    public function setBacdr240(int $bacdr240): static
    {
        $this->bacdr240 = $bacdr240;

        return $this;
    }

    public function getBacrecycle240(): ?int
    {
        return $this->bacrecycle240;
    }

    public function setBacrecycle240(int $bacrecycle240): static
    {
        $this->bacrecycle240 = $bacrecycle240;

        return $this;
    }

    public function getBacrecycle660(): ?int
    {
        return $this->bacrecycle660;
    }

    public function setBacrecycle660(int $bacrecycle660): static
    {
        $this->bacrecycle660 = $bacrecycle660;

        return $this;
    }

    public function getBacbiodechet120(): ?int
    {
        return $this->bacbiodechet120;
    }

    public function setBacbiodechet120(int $bacbiodechet120): static
    {
        $this->bacbiodechet120 = $bacbiodechet120;

        return $this;
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
            $suivicentredetri->setCentredetri($this);
        }

        return $this;
    }

    public function removeSuivicentredetri(Suivicentredetri $suivicentredetri): static
    {
        if ($this->suivicentredetris->removeElement($suivicentredetri)) {
            // set the owning side to null (unless already changed)
            if ($suivicentredetri->getCentredetri() === $this) {
                $suivicentredetri->setCentredetri(null);
            }
        }

        return $this;
    }

    public function getBachenoires(): ?int
    {
        return $this->bachenoires;
    }

    public function setBachenoires(int $bachenoires): static
    {
        $this->bachenoires = $bachenoires;

        return $this;
    }
    public function getBennebois(): ?int
    {
        return $this->bennebois;
    }

    public function setBennebois(int $bennebois): static
    {
        $this->bennebois = $bennebois;

        return $this;
    }

    public function getBennecarton(): ?int
    {
        return $this->bennecarton;
    }

    public function setBennecarton(int $bennecarton): static
    {
        $this->bennecarton = $bennecarton;

        return $this;
    }

    public function getBenneplastiques(): ?int
    {
        return $this->benneplastiques;
    }

    public function setBenneplastiques(int $benneplastiques): static
    {
        $this->benneplastiques = $benneplastiques;

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
            $suivi->setCentredetris($this);
        }

        return $this;
    }

    public function removeSuivi(Suivi $suivi): static
    {
        if ($this->suivis->removeElement($suivi)) {
            // set the owning side to null (unless already changed)
            if ($suivi->getCentredetris() === $this) {
                $suivi->setCentredetris(null);
            }
        }

        return $this;
    }

    public function getSite(): ?Site
    {
        return $this->site;
    }

    public function setSite(?Site $site): static
    {
        $this->site = $site;

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
            $photosarchive->setCentredetri($this);
        }

        return $this;
    }

    public function removePhotosarchive(Photosarchives $photosarchive): static
    {
        if ($this->photosarchives->removeElement($photosarchive)) {
            // set the owning side to null (unless already changed)
            if ($photosarchive->getCentredetri() === $this) {
                $photosarchive->setCentredetri(null);
            }
        }

        return $this;
    }

}
