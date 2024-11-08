<?php

namespace App\Entity;

use App\Repository\HallRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: HallRepository::class)]
class Hall
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    /**
     * @var Collection<int, Suivi>
     */
    #[ORM\OneToMany(targetEntity: Suivi::class, mappedBy: 'hall')]
    private Collection $lessuivis;

    #[ORM\ManyToOne(inversedBy: 'leshalls')]
    private ?Site $site = null;

    #[ORM\ManyToOne(inversedBy: 'leshalls')]
    private ?Client $client = null;

    /**
     * @var Collection<int, Evenement>
     */
    #[ORM\ManyToMany(targetEntity: Evenement::class, mappedBy: 'leshalls')]
    private Collection $evenements;

    #[ORM\ManyToOne(inversedBy: 'halls')]
    private ?Centredetri $centredetri = null;

    /**
     * @var Collection<int, Donation>
     */
    #[ORM\OneToMany(targetEntity: Donation::class, mappedBy: 'Hallentity')]
    private Collection $donations;

    public function __construct()
    {
        $this->lessuivis = new ArrayCollection();
        $this->evenements = new ArrayCollection();
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

    /**
     * @return Collection<int, Suivi>
     */
    public function getLessuivis(): Collection
    {
        return $this->lessuivis;
    }

    public function addLessuivi(Suivi $lessuivi): static
    {
        if (!$this->lessuivis->contains($lessuivi)) {
            $this->lessuivis->add($lessuivi);
            $lessuivi->setHall($this);
        }

        return $this;
    }

    public function removeLessuivi(Suivi $lessuivi): static
    {
        if ($this->lessuivis->removeElement($lessuivi)) {
            // set the owning side to null (unless already changed)
            if ($lessuivi->getHall() === $this) {
                $lessuivi->setHall(null);
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

    public function __toString(): string
    {
        // Concaténer le nom avec une autre variable
        return $this->nom . ' (' . $this->site->getNom(). ')' ;
    }

    public function getClient(): ?Client
    {
        return $this->client;
    }

    public function setClient(?Client $client): static
    {
        $this->client = $client;

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
            $evenement->addLeshall($this);
        }

        return $this;
    }

    public function removeEvenement(Evenement $evenement): static
    {
        if ($this->evenements->removeElement($evenement)) {
            $evenement->removeLeshall($this);
        }

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
            $donation->setHallentity($this);
        }

        return $this;
    }

    public function removeDonation(Donation $donation): static
    {
        if ($this->donations->removeElement($donation)) {
            // set the owning side to null (unless already changed)
            if ($donation->getHallentity() === $this) {
                $donation->setHallentity(null);
            }
        }

        return $this;
    }

}
