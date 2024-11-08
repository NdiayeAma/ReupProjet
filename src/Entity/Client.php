<?php

namespace App\Entity;

use App\Repository\ClientRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ClientRepository::class)]
class Client
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
    #[ORM\OneToMany(targetEntity: Hall::class, mappedBy: 'client')]
    private Collection $leshalls;

    /**
     * @var Collection<int, Site>
     */
    #[ORM\ManyToMany(targetEntity: Site::class, mappedBy: 'leclient')]
    private Collection $sites;

    /**
     * @var Collection<int, Suivi>
     */
    #[ORM\OneToMany(targetEntity: Suivi::class, mappedBy: 'leclient')]
    private Collection $suivis;

    /**
     * @var Collection<int, Evenement>
     */
    #[ORM\OneToMany(targetEntity: Evenement::class, mappedBy: 'leclient')]
    private Collection $evenements;

   

    public function __construct()
    {
        $this->sites = new ArrayCollection();
        $this->leshalls = new ArrayCollection();
        $this->suivis = new ArrayCollection();
        $this->evenements = new ArrayCollection();
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
            $site->addLeclient($this);
        }

        return $this;
    }

    public function removeSite(Site $site): static
    {
        $site->removeLeclient($this);
        $this->sites->removeElement($site);

        return $this;
    }
    public function __toString(): string
    {

        return $this->nom; // Remplacez "nom" par le nom de la propriété que vous souhaitez afficher
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
            $leshall->setClient($this);
        }

        return $this;
    }

    public function removeLeshall(Hall $leshall): static
    {
        if ($this->leshalls->removeElement($leshall)) {
            // set the owning side to null (unless already changed)
            if ($leshall->getClient() === $this) {
                $leshall->setClient(null);
            }
        }

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
            $suivi->setLeclient($this);
        }

        return $this;
    }

    public function removeSuivi(Suivi $suivi): static
    {
        if ($this->suivis->removeElement($suivi)) {
            // set the owning side to null (unless already changed)
            if ($suivi->getLeclient() === $this) {
                $suivi->setLeclient(null);
            }
        }

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
            $evenement->setLeclient($this);
        }

        return $this;
    }

    public function removeEvenement(Evenement $evenement): static
    {
        if ($this->evenements->removeElement($evenement)) {
            // set the owning side to null (unless already changed)
            if ($evenement->getLeclient() === $this) {
                $evenement->setLeclient(null);
            }
        }

        return $this;
    }



}
