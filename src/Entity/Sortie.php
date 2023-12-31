<?php

namespace App\Entity;

use App\Enum\Etat;
use App\Repository\SortieRepository;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
#[ORM\Entity(repositoryClass: SortieRepository::class)]
class Sortie
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    #[Assert\Length(
        min:1,
        max: 20,
        minMessage: "Le nom doit avoir entre 1 et 20 caractères.",
        maxMessage: "Le nom doit avoir entre 1 et 20 caractères.")]
    #[Assert\Regex(pattern: "/\S/", message: "Le nom ne peut pas contenir uniquement des espaces", match: true)]
    private ?string $nom = null;



    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Assert\GreaterThan("today", message: "La date doit être supérieure à la date actuelle")]

    private ?DateTimeInterface $dateHeureDebut = null;

    #[ORM\Column]
    #[Assert\GreaterThan(0, message: "La durée doit être supérieur à 0.")]
    private ?int $duree = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Assert\GreaterThan("today", message: "La date doit être supérieure à la date actuelle.")]
    #[Assert\Expression("this.getDateLimiteInscription() < this.getDateHeureDebut()", message: "La date limite d'inscription doit être inférieure à la date de la sortie.")]
    private ?DateTimeInterface $dateLimiteInscription = null;

    #[ORM\Column]
    #[Assert\GreaterThan(0, message: "Le nombre de participant doit être supérieur à 0.")]
    private ?int $nbInscriptionsMax = null;

    #[ORM\Column(length: 255)]
    #[Assert\Length(
        min:19,
        max: 255,
        minMessage: "La description doit avoir entre 20 et 255 caractères.",
        maxMessage: "La description doit avoir entre 20 et 255 caractères.")]
    private ?string $infosSortie = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Lieu $lieu = null;

    #[ORM\ManyToOne(inversedBy: 'sorties')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Site $site = null;

    #[ORM\ManyToOne(inversedBy: 'sortiesOrganisee')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Participant $organisateur = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $motifAnnulation = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $photoSortie = null;

    #[ORM\Column(length: 255, nullable: true)]
    private $etat;

    #[ORM\ManyToMany(targetEntity: Participant::class, inversedBy: 'sorties')]
    private Collection $participantsInscrits;

    public function __construct()
    {
        $this->participantsInscrits = new ArrayCollection();
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

    public function getEtat(): Etat
    {
        return new Etat($this->etat);
    }

    public function setEtat(string $etat): static
    {
        $this->etat = $etat;

        return $this;
    }

    public function getDateHeureDebut(): ?DateTimeInterface
    {
        return $this->dateHeureDebut;
    }

    public function setDateHeureDebut(DateTimeInterface $dateHeureDebut): static
    {
        $this->dateHeureDebut = $dateHeureDebut;

        return $this;
    }

    public function getDuree(): ?int
    {
        return $this->duree;
    }

    public function setDuree(int $duree): static
    {
        $this->duree = $duree;

        return $this;
    }

    public function getDateLimiteInscription(): ?DateTimeInterface
    {
        return $this->dateLimiteInscription;
    }

    public function setDateLimiteInscription(DateTimeInterface $dateLimiteInscription): static
    {
        $this->dateLimiteInscription = $dateLimiteInscription;

        return $this;
    }

    public function getNbInscriptionsMax(): ?int
    {
        return $this->nbInscriptionsMax;
    }

    public function setNbInscriptionsMax(int $nbInscriptionsMax): static
    {
        $this->nbInscriptionsMax = $nbInscriptionsMax;

        return $this;
    }

    public function getInfosSortie(): ?string
    {
        return $this->infosSortie;
    }

    public function setInfosSortie(string $infosSortie): static
    {
        $this->infosSortie = $infosSortie;

        return $this;
    }

    public function getLieu(): ?Lieu
    {
        return $this->lieu;
    }

    public function setLieu(?Lieu $lieu): static
    {
        $this->lieu = $lieu;

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

    public function getOrganisateur(): ?Participant
    {
        return $this->organisateur;
    }

    public function setOrganisateur(?Participant $organisateur): static
    {
        $this->organisateur = $organisateur;

        return $this;
    }
    public function getMotifAnnulation(): ?string
    {
        return $this->motifAnnulation;
    }

    public function setMotifAnnulation(?string $motifAnnulation): static
    {
        $this->motifAnnulation = $motifAnnulation;

        return $this;
    }

    public function getPhotoSortie(): ?string
    {
        return $this->photoSortie;
    }

    public function setPhotoSortie(?string $photoSortie): static
    {
        $this->photoSortie = $photoSortie;

        return $this;
    }

    /**
     * @return Collection<int, Participant>
     */
    public function getParticipantsInscrits(): Collection
    {
        return $this->participantsInscrits;
    }

    public function addParticipantsInscrit(Participant $participantsInscrit): static
    {
        if (!$this->participantsInscrits->contains($participantsInscrit)) {
            $this->participantsInscrits->add($participantsInscrit);
        }

        return $this;
    }

    public function removeParticipantsInscrit(Participant $participantsInscrit): static
    {
        $this->participantsInscrits->removeElement($participantsInscrit);

        return $this;
    }
}
