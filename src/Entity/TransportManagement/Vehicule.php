<?php

namespace App\Entity\TransportManagement;

use App\Entity\LovManagement\Marque;
use App\Entity\LovManagement\Quartier;
use App\Entity\LovManagement\TypeVoiture;
use App\Entity\LovManagement\Ville;
use App\Entity\UserManagement\User;
use App\Repository\TransportManagement\VehiculeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use DH\DoctrineAuditBundle\Annotation as Audit;


/**
 * Class Category.
 * @ORM\HasLifecycleCallbacks()
 * @Audit\Auditable
 * @Audit\Security(view={"ROLE_ADMIN"}) 
 * @ORM\Table(name="vehicule")
 * @ORM\Entity
 */
class Vehicule
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $nom;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $matricule;

    /**
     * @ORM\Column(type="text")
     */
    private $description;

    /**
     * @ORM\ManyToOne(targetEntity=TypeVoiture::class, inversedBy="vehicule")
     * @ORM\JoinColumn(nullable=false)
     */
    private $type;

    /**
     * @ORM\ManyToOne(targetEntity=Marque::class, inversedBy="vehicules")
     * @ORM\JoinColumn(nullable=false)
     */
    private $marque;

    /**
     * @ORM\ManyToMany(targetEntity=User::class, inversedBy="vehicules")
     */
    private $proprietaire;

    /**
     * @ORM\ManyToMany(targetEntity=Ville::class, inversedBy="vehicules")
     */
    private $ville;

    /**
     * @ORM\ManyToMany(targetEntity=Quartier::class, inversedBy="charge")
     */
    private $quartier;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $charge;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $longueur;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $largeur;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $hauteur;

    public function __construct()
    {
        $this->proprietaire = new ArrayCollection();
        $this->ville = new ArrayCollection();
        $this->quartier = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(?string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getMatricule(): ?string
    {
        return $this->matricule;
    }

    public function setMatricule(string $matricule): self
    {
        $this->matricule = $matricule;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getType(): ?TypeVoiture
    {
        return $this->type;
    }

    public function setType(?TypeVoiture $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getMarque(): ?Marque
    {
        return $this->marque;
    }

    public function setMarque(?Marque $marque): self
    {
        $this->marque = $marque;

        return $this;
    }

    /**
     * @return Collection|User[]
     */
    public function getProprietaire(): Collection
    {
        return $this->proprietaire;
    }

    public function addProprietaire(User $proprietaire): self
    {
        if (!$this->proprietaire->contains($proprietaire)) {
            $this->proprietaire[] = $proprietaire;
        }

        return $this;
    }

    public function removeProprietaire(User $proprietaire): self
    {
        $this->proprietaire->removeElement($proprietaire);

        return $this;
    }

    /**
     * @return Collection|Ville[]
     */
    public function getVille(): Collection
    {
        return $this->ville;
    }

    public function addVille(Ville $ville): self
    {
        if (!$this->ville->contains($ville)) {
            $this->ville[] = $ville;
        }

        return $this;
    }

    public function removeVille(Ville $ville): self
    {
        $this->ville->removeElement($ville);

        return $this;
    }

    /**
     * @return Collection|Quartier[]
     */
    public function getQuartier(): Collection
    {
        return $this->quartier;
    }

    public function addQuartier(Quartier $quartier): self
    {
        if (!$this->quartier->contains($quartier)) {
            $this->quartier[] = $quartier;
        }

        return $this;
    }

    public function removeQuartier(Quartier $quartier): self
    {
        $this->quartier->removeElement($quartier);

        return $this;
    }

    public function getCharge(): ?int
    {
        return $this->charge;
    }

    public function setCharge(?int $charge): self
    {
        $this->charge = $charge;

        return $this;
    }

    public function getLongueur(): ?int
    {
        return $this->longueur;
    }

    public function setLongueur(?int $longueur): self
    {
        $this->longueur = $longueur;

        return $this;
    }

    public function getLargeur(): ?int
    {
        return $this->largeur;
    }

    public function setLargeur(?int $largeur): self
    {
        $this->largeur = $largeur;

        return $this;
    }

    public function getHauteur(): ?int
    {
        return $this->hauteur;
    }

    public function setHauteur(?int $hauteur): self
    {
        $this->hauteur = $hauteur;

        return $this;
    }
}
