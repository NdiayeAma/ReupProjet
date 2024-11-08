<?php

namespace App\Entity;

use App\Repository\SiteRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SiteRepository::class)]
class Site
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    /**
     * @var Collection<int, Hall>
     */
    #[ORM\OneToMany(targetEntity: Hall::class, mappedBy: 'site')]
    private Collection $leshalls;

    /**
     * @var Collection<int, Client>
     */
    #[ORM\ManyToMany(targetEntity: Client::class, inversedBy: 'sites')]
    private Collection $leclient;

    /**
     * @var Collection<int, Evenement>
     */
    #[ORM\ManyToMany(targetEntity: Evenement::class, mappedBy: 'sites')]
    private Collection $evenements;

    /**
     * @var Collection<int, Centredetri>
     */
    #[ORM\OneToMany(targetEntity: Centredetri::class, mappedBy: 'site')]
    private Collection $centredetris;






    public function __construct()
    {
        $this->leshalls = new ArrayCollection();
        $this->leclient = new ArrayCollection();
        $this->evenements = new ArrayCollection();
        $this->centredetris = new ArrayCollection();
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
            $leshall->setSite($this);
        }

        return $this;
    }

    public function removeLeshall(Hall $leshall): static
    {
        if ($this->leshalls->removeElement($leshall)) {
            // set the owning side to null (unless already changed)
            if ($leshall->getSite() === $this) {
                $leshall->setSite(null);
            }
        }

        return $this;
    }

    public function getLeclient(): Collection
    {
        return $this->leclient;
    }



    public function __toString(): string
    {
        return $this->nom; // Remplacez "nom" par le nom de la propriété que vous souhaitez afficher
    }

    public function addLeclient(Client $leclient): static
    {
        if (!$this->leclient->contains($leclient)) {
            $this->leclient->add($leclient);
        }

        return $this;
    }

    public function removeLeclient(Client $leclient): static
    {
        $this->leclient->removeElement($leclient);

        return $this;
    }

    /**
     * @return Collection<int, Evenement>
     */
    public function getEvenements(): Collection
    {
        return $this->evenements;
    }

    public function addEvenement(Evenement $evenement): static
    {
        if (!$this->evenements->contains($evenement)) {
            $this->evenements->add($evenement);
            $evenement->addSite($this);
        }

        return $this;
    }

    public function removeEvenement(Evenement $evenement): static
    {
        if ($this->evenements->removeElement($evenement)) {
            $evenement->removeSite($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, Centredetri>
     */
    public function getCentredetris(): Collection
    {
        return $this->centredetris;
    }

    public function addCentredetri(Centredetri $centredetri): static
    {
        if (!$this->centredetris->contains($centredetri)) {
            $this->centredetris->add($centredetri);
            $centredetri->setSite($this);
        }

        return $this;
    }

    public function removeCentredetri(Centredetri $centredetri): static
    {
        if ($this->centredetris->removeElement($centredetri)) {
            // set the owning side to null (unless already changed)
            if ($centredetri->getSite() === $this) {
                $centredetri->setSite(null);
            }
        }

        return $this;
    }



}
