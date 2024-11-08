<?php

namespace App\Entity;

use App\Repository\SuivicentredetriRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SuivicentredetriRepository::class)]
class Suivicentredetri
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $commentaire = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $datedesoumission = null;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getDatedesoumission(): ?\DateTimeInterface
    {
        return $this->datedesoumission;
    }

    public function setDatedesoumission(\DateTimeInterface $datedesoumission): static
    {
        $this->datedesoumission = $datedesoumission;

        return $this;
    }



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
    private ?string $vidoirliquide = 'manquant';

    #[ORM\Column]
    private ?string $troussedesecours = 'manquant';

    #[ORM\Column]
    private ?string $epi = 'manquant';

    #[ORM\Column]
    private ?string $materielsdenettoyage = 'manquant';



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
    private ?int $bennebois = null;

    #[ORM\Column]
    private ?int $bennecarton = null;

    #[ORM\Column]
    private ?int $benneplastiques = null;

    /**
     * @var Collection<int, Photosarchives>
     */
    #[ORM\ManyToMany(targetEntity: Photosarchives::class, inversedBy: 'suivicentredetris')]
    private Collection $photos;

    #[ORM\ManyToOne(inversedBy: 'suivicentredetris')]
    private ?Centredetri $centredetri = null;

    #[ORM\Column]
    private ?int $bachenoires = null;

    public function __construct()
    {
        $this->photos = new ArrayCollection();
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

    public function getVidoirliquide(): ?string
    {
        return $this->vidoirliquide;
    }

    public function setVidoirliquide(string $vidoirliquide): static
    {
        $this->vidoirliquide = $vidoirliquide;

        return $this;
    }

    public function getTroussedesecours(): ?string
    {
        return $this->troussedesecours;
    }

    public function setTroussedesecours(string $troussedesecours): static
    {
        $this->troussedesecours = $troussedesecours;

        return $this;
    }

    public function getEpi(): ?string
    {
        return $this->epi;
    }

    public function setEpi(string $epi): static
    {
        $this->epi = $epi;

        return $this;
    }

    public function getMaterielsdenettoyage(): ?string
    {
        return $this->materielsdenettoyage;
    }

    public function setMaterielsdenettoyage(string $materielsdenettoyage): static
    {
        $this->materielsdenettoyage = $materielsdenettoyage;

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

    public function getCentredetri(): ?Centredetri
    {
        return $this->centredetri;
    }

    public function setCentredetri(?Centredetri $centredetri): static
    {
        $this->centredetri = $centredetri;

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
    
}
