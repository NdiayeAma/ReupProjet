<?php

namespace App\Entity;

use App\Repository\InventaireRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: InventaireRepository::class)]
class Inventaire
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    #[ORM\Column(type: 'float')]
    private $longueur;

    #[ORM\Column(type: 'float')]
    private $largeur;

    #[ORM\Column(type: 'float')]
    private $hauteur;

    /**
     * @return mixed
     */
    public function getLongueur()
    {
        return $this->longueur;
    }

    /**
     * @param mixed $longueur
     */
    public function setLongueur($longueur): void
    {
        $this->longueur = $longueur;
    }

    /**
     * @return mixed
     */
    public function getLargeur()
    {
        return $this->largeur;
    }

    /**
     * @param mixed $largeur
     */
    public function setLargeur($largeur): void
    {
        $this->largeur = $largeur;
    }

    /**
     * @return mixed
     */
    public function getHauteur()
    {
        return $this->hauteur;
    }

    /**
     * @param mixed $hauteur
     */
    public function setHauteur($hauteur): void
    {
        $this->hauteur = $hauteur;
    }

    /**
     * @return mixed
     */
    public function getDiametre()
    {
        return $this->diametre;
    }

    /**
     * @param mixed $diametre
     */
    public function setDiametre($diametre): void
    {
        $this->diametre = $diametre;
    }

    /**
     * @return mixed
     */
    public function getUniteDiametre()
    {
        return $this->uniteDiametre;
    }

    /**
     * @param mixed $uniteDiametre
     */
    public function setUniteDiametre($uniteDiametre): void
    {
        $this->uniteDiametre = $uniteDiametre;
    }

    /**
     * @return mixed
     */
    public function getMateriaux()
    {
        return $this->materiaux;
    }

    /**
     * @param mixed $materiaux
     */
    public function setMateriaux($materiaux): void
    {
        $this->materiaux = $materiaux;
    }

    /**
     * @return mixed
     */
    public function getColor1()
    {
        return $this->color1;
    }

    /**
     * @param mixed $color1
     */
    public function setColor1($color1): void
    {
        $this->color1 = $color1;
    }

    /**
     * @return mixed
     */
    public function getMarque()
    {
        return $this->marque;
    }

    /**
     * @param mixed $marque
     */
    public function setMarque($marque): void
    {
        $this->marque = $marque;
    }

    /**
     * @return mixed
     */
    public function getModele()
    {
        return $this->modele;
    }

    /**
     * @param mixed $modele
     */
    public function setModele($modele): void
    {
        $this->modele = $modele;
    }

    /**
     * @return mixed
     */
    public function getEtat()
    {
        return $this->etat;
    }

    /**
     * @param mixed $etat
     */
    public function setEtat($etat): void
    {
        $this->etat = $etat;
    }

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param mixed $description
     */
    public function setDescription($description): void
    {
        $this->description = $description;
    }

    /**
     * @return mixed
     */
    public function getReemploi()
    {
        return $this->reemploi;
    }

    /**
     * @param mixed $reemploi
     */
    public function setReemploi($reemploi): void
    {
        $this->reemploi = $reemploi;
    }

    /**
     * @return mixed
     */
    public function getReutilisation()
    {
        return $this->reutilisation;
    }

    /**
     * @param mixed $reutilisation
     */
    public function setReutilisation($reutilisation): void
    {
        $this->reutilisation = $reutilisation;
    }

    /**
     * @return mixed
     */
    public function getRecyclage()
    {
        return $this->recyclage;
    }

    /**
     * @param mixed $recyclage
     */
    public function setRecyclage($recyclage): void
    {
        $this->recyclage = $recyclage;
    }

    /**
     * @return mixed
     */
    public function getPrecisions()
    {
        return $this->precisions;
    }

    /**
     * @param mixed $precisions
     */
    public function setPrecisions($precisions): void
    {
        $this->precisions = $precisions;
    }

    /**
     * @return mixed
     */
    public function getModeAssemblage()
    {
        return $this->modeAssemblage;
    }

    /**
     * @param mixed $modeAssemblage
     */
    public function setModeAssemblage($modeAssemblage): void
    {
        $this->modeAssemblage = $modeAssemblage;
    }

    /**
     * @return mixed
     */
    public function getMethodologieDepose()
    {
        return $this->methodologieDepose;
    }

    /**
     * @param mixed $methodologieDepose
     */
    public function setMethodologieDepose($methodologieDepose): void
    {
        $this->methodologieDepose = $methodologieDepose;
    }

    /**
     * @return mixed
     */
    public function getModaliteTransport()
    {
        return $this->modaliteTransport;
    }

    /**
     * @param mixed $modaliteTransport
     */
    public function setModaliteTransport($modaliteTransport): void
    {
        $this->modaliteTransport = $modaliteTransport;
    }

    /**
     * @return mixed
     */
    public function getConditionnement()
    {
        return $this->conditionnement;
    }

    /**
     * @param mixed $conditionnement
     */
    public function setConditionnement($conditionnement): void
    {
        $this->conditionnement = $conditionnement;
    }

    /**
     * @return mixed
     */
    public function getRisquesPrecautions()
    {
        return $this->risquesPrecautions;
    }

    /**
     * @param mixed $risquesPrecautions
     */
    public function setRisquesPrecautions($risquesPrecautions): void
    {
        $this->risquesPrecautions = $risquesPrecautions;
    }

    /**
     * @return mixed
     */
    public function getEtape()
    {
        return $this->etape;
    }

    /**
     * @param mixed $etape
     */
    public function setEtape($etape): void
    {
        $this->etape = $etape;
    }

    /**
     * @return mixed
     */
    public function getQuantite()
    {
        return $this->quantite;
    }

    /**
     * @param mixed $quantite
     */
    public function setQuantite($quantite): void
    {
        $this->quantite = $quantite;
    }

    /**
     * @return mixed
     */
    public function getUniteQuantite()
    {
        return $this->uniteQuantite;
    }

    /**
     * @param mixed $uniteQuantite
     */
    public function setUniteQuantite($uniteQuantite): void
    {
        $this->uniteQuantite = $uniteQuantite;
    }

    /**
     * @return mixed
     */
    public function getActions()
    {
        return $this->actions;
    }

    /**
     * @param mixed $actions
     */
    public function setActions($actions): void
    {
        $this->actions = $actions;
    }

    /**
     * @return mixed
     */
    public function getDateDebut()
    {
        return $this->dateDebut;
    }

    /**
     * @param mixed $dateDebut
     */
    public function setDateDebut($dateDebut): void
    {
        $this->dateDebut = $dateDebut;
    }

    /**
     * @return mixed
     */
    public function getDateFin()
    {
        return $this->dateFin;
    }

    /**
     * @param mixed $dateFin
     */
    public function setDateFin($dateFin): void
    {
        $this->dateFin = $dateFin;
    }

    /**
     * @return mixed
     */
    public function getLocalisation()
    {
        return $this->localisation;
    }

    /**
     * @param mixed $localisation
     */
    public function setLocalisation($localisation): void
    {
        $this->localisation = $localisation;
    }

    #[ORM\Column(type: 'float', nullable: true)]
    private $diametre;

    #[ORM\Column(type: 'string', length: 50, nullable: true)]
    private $uniteDiametre;

    #[ORM\Column(type: 'string', length: 255)]
    private $materiaux;

    #[ORM\Column(type: 'string', length: 7)]
    private $color1;

    #[ORM\Column(type: 'string', length: 255)]
    private $marque;

    #[ORM\Column(type: 'string', length: 255)]
    private $modele;

    #[ORM\Column(type: 'string', length: 50)]
    private $etat;

    #[ORM\Column(type: 'text')]
    private $description;

    #[ORM\Column(type: 'string', length: 50)]
    private $reemploi;

    #[ORM\Column(type: 'string', length: 50)]
    private $reutilisation;

    #[ORM\Column(type: 'string', length: 50)]
    private $recyclage;

    #[ORM\Column(type: 'text', nullable: true)]
    private $precisions;

    #[ORM\Column(type: 'text', nullable: true)]
    private $modeAssemblage;

    #[ORM\Column(type: 'text', nullable: true)]
    private $methodologieDepose;

    #[ORM\Column(type: 'text', nullable: true)]
    private $modaliteTransport;

    #[ORM\Column(type: 'text', nullable: true)]
    private $conditionnement;

    #[ORM\Column(type: 'text', nullable: true)]
    private $risquesPrecautions;

    #[ORM\Column(type: 'string', length: 50)]
    private $etape;

    #[ORM\Column(type: 'float')]
    private $quantite;

    #[ORM\Column(type: 'string', length: 50)]
    private $uniteQuantite;

    #[ORM\Column(type: 'string', length: 50)]
    private $actions;

    #[ORM\Column(type: 'datetime')]
    private $dateDebut;

    #[ORM\Column(type: 'datetime')]
    private $dateFin;

    #[ORM\Column(type: 'string', length: 255)]
    private $localisation;
}
