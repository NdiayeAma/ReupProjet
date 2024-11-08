<?php

namespace App\Entity;

use App\Repository\DonationRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DonationRepository::class)]
class Donation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;
    #[ORM\Column(type: Types::STRING, length: 255)]
    private ?string $companyName = null;

    #[ORM\Column(type: Types::STRING, length: 255)]
    private ?string $hall = null;

    #[ORM\Column(type: Types::STRING, length: 255)]
    private ?string $aisleBoothNumber = null;

    #[ORM\Column(type: Types::STRING, length: 255)]
    private ?string $contactBuilder = null;

    #[ORM\Column(type: Types::BOOLEAN)]
    private ?bool $csrFormDownloaded = null;

    #[ORM\Column(type: Types::BOOLEAN)]
    private ?bool $donateMaterials = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $dateupload = null;

    #[ORM\Column(type: Types::BOOLEAN)]
    private ?bool $donateWood = null;

    #[ORM\Column(type: Types::JSON, nullable: true)]
    private ?array $woodTypes = [];

    #[ORM\Column(type: Types::JSON, nullable: true)]
    private ?array $woodQuantities = null;

    #[ORM\Column(type: Types::STRING, nullable: true)]
    private ?string $furnitureQuantity = null;

    #[ORM\Column(type: Types::STRING, nullable: true)]
    private ?string $otherMaterialsQuantity = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $comments = null;

    #[ORM\ManyToOne(inversedBy: 'donations')]
    private ?Evenement $evenement = null;

    #[ORM\Column(nullable: true)]
    private ?bool $confirmation = null;

    #[ORM\Column(nullable: true)]
    private ?bool $reception = null;

    #[ORM\Column(nullable: true)]
    private ?bool $sensibilisation = null;

    #[ORM\ManyToOne(inversedBy: 'donations')]
    private ?Hall $Hallentity = null;



    // Getters et setters pour chaque champ...

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCompanyName(): ?string
    {
        return $this->companyName;
    }

    public function setCompanyName(string $companyName): self
    {
        $this->companyName = $companyName;

        return $this;
    }

    public function getHall(): ?string
    {
        return $this->hall;
    }

    public function setHall(string $hall): self
    {
        $this->hall = $hall;

        return $this;
    }

    public function getAisleBoothNumber(): ?string
    {
        return $this->aisleBoothNumber;
    }

    public function setAisleBoothNumber(string $aisleBoothNumber): self
    {
        $this->aisleBoothNumber = $aisleBoothNumber;

        return $this;
    }

    public function getContactBuilder(): ?string
    {
        return $this->contactBuilder;
    }

    public function setContactBuilder(string $contactBuilder): self
    {
        $this->contactBuilder = $contactBuilder;

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

    public function getCsrFormDownloaded(): ?bool
    {
        return $this->csrFormDownloaded;
    }

    public function setCsrFormDownloaded(bool $csrFormDownloaded): self
    {
        $this->csrFormDownloaded = $csrFormDownloaded;

        return $this;
    }

    public function getDonateMaterials(): ?bool
    {
        return $this->donateMaterials;
    }

    public function setDonateMaterials(bool $donateMaterials): self
    {
        $this->donateMaterials = $donateMaterials;

        return $this;
    }

    public function getDonateWood(): ?bool
    {
        return $this->donateWood;
    }

    public function setDonateWood(bool $donateWood): self
    {
        $this->donateWood = $donateWood;

        return $this;
    }

    public function getWoodTypes(): ?array
    {
        return $this->woodTypes;
    }

    public function setWoodTypes(?array $woodTypes): self
    {
        $this->woodTypes = $woodTypes;

        return $this;
    }

    public function getWoodQuantities(): ?array
    {
        return $this->woodQuantities;
    }

    public function setWoodQuantities(?array $woodQuantities): self
    {
        $this->woodQuantities = $woodQuantities;

        return $this;
    }

    public function getFurnitureQuantity(): ?string
    {
        return $this->furnitureQuantity;
    }

    public function setFurnitureQuantity(?string $furnitureQuantity): self
    {
        $this->furnitureQuantity = $furnitureQuantity;

        return $this;
    }

    public function getOtherMaterialsQuantity(): ?string
    {
        return $this->otherMaterialsQuantity;
    }

    public function setOtherMaterialsQuantity(?string $otherMaterialsQuantity): self
    {
        $this->otherMaterialsQuantity = $otherMaterialsQuantity;

        return $this;
    }

    public function getComments(): ?string
    {
        return $this->comments;
    }

    public function setComments(?string $comments): self
    {
        $this->comments = $comments;

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

    public function isConfirmation(): ?bool
    {
        return $this->confirmation;
    }

    public function setConfirmation(?bool $confirmation): static
    {
        $this->confirmation = $confirmation;

        return $this;
    }

    public function isReception(): ?bool
    {
        return $this->reception;
    }

    public function setReception(?bool $reception): static
    {
        $this->reception = $reception;

        return $this;
    }

    public function isSensibilisation(): ?bool
    {
        return $this->sensibilisation;
    }

    public function setSensibilisation(bool $sensibilisation): static
    {
        $this->sensibilisation = $sensibilisation;

        return $this;
    }

    public function getHallentity(): ?Hall
    {
        return $this->Hallentity;
    }

    public function setHallentity(?Hall $Hallentity): static
    {
        $this->Hallentity = $Hallentity;

        return $this;
    }


}
