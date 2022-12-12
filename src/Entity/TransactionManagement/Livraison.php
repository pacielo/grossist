<?php

namespace App\Entity\TransactionManagement;

use App\Entity\LovManagement\Etat;
use App\Entity\UserManagement\User;
use App\Traits\ActorTrait;
use App\Traits\DateTrait;
use App\Traits\IsValidTrait;
use App\Traits\RevisionTrait;
use App\Repository\TransactionManagement\LivraisonRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=LivraisonRepository::class)
 */
class Livraison
{
    use ActorTrait;
    use RevisionTrait;
    use DateTrait;
    use IsValidTrait;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $departAdress;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $arriveAdress;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $departHeure;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $arriveHeure;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $prix;

    /**
     * @ORM\ManyToOne(targetEntity=Etat::class, inversedBy="livraisons")
     * @ORM\JoinColumn(nullable=false)
     */
    private $etat;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="livraisons")
     */
    private $transporteur;

    /**
     * @ORM\OneToMany(targetEntity=PurchaseItem::class, mappedBy="livraison")
     */
    private $purchasedItems;

    /**
     * The Livraison parent.
     *
     * @var Livraison
     * @ORM\ManyToOne(targetEntity="Livraison")
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id", nullable=true)
     **/
    protected $parent;

    public function __construct()
    {
        $this->sort = 0;
        $this->isValid = 1;
        $this->revision = -1;
        $this->purchasedItems = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->getDepartAdress();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDepartAdress(): ?string
    {
        return $this->departAdress;
    }

    public function setDepartAdress(string $departAdress): self
    {
        $this->departAdress = $departAdress;

        return $this;
    }

    public function getArriveAdress(): ?string
    {
        return $this->arriveAdress;
    }

    public function setArriveAdress(string $arriveAdress): self
    {
        $this->arriveAdress = $arriveAdress;

        return $this;
    }

    public function getDepartHeure(): ?\DateTimeInterface
    {
        return $this->departHeure;
    }

    public function setDepartHeure(?\DateTimeInterface $departHeure): self
    {
        $this->departHeure = $departHeure;

        return $this;
    }

    public function getArriveHeure(): ?\DateTimeInterface
    {
        return $this->arriveHeure;
    }

    public function setArriveHeure(?\DateTimeInterface $arriveHeure): self
    {
        $this->arriveHeure = $arriveHeure;

        return $this;
    }

    public function getPrix(): ?int
    {
        return $this->prix;
    }

    public function setPrix(?int $prix): self
    {
        $this->prix = $prix;

        return $this;
    }

    public function getEtat(): ?Etat
    {
        return $this->etat;
    }

    public function setEtat(?Etat $etat): self
    {
        $this->etat = $etat;

        return $this;
    }

    public function getTransporteur() 
    {
        return $this->transporteur;
    }

    public function setTransporteur(?User $transporteur): self
    {
        $this->transporteur = $transporteur;

        return $this;
    }

    /**
     * @return Collection|PurchaseItem[]
     */
    public function getPurchasedItems(): Collection
    {
        return $this->purchasedItems;
    }

    public function addPurchasedItem(PurchaseItem $purchasedItem): self
    {
        if (!$this->purchasedItems->contains($purchasedItem)) {
            $this->purchasedItems[] = $purchasedItem;
            $purchasedItem->setLivraison($this);
        }

        return $this;
    }

    public function removePurchasedItem(PurchaseItem $purchasedItem): self
    {
        if ($this->purchasedItems->removeElement($purchasedItem)) {
            // set the owning side to null (unless already changed)
            if ($purchasedItem->getLivraison() === $this) {
                $purchasedItem->setLivraison(null);
            }
        }

        return $this;
    }

    /**
     * Set the parent Livraison.
     *
     * @param Livraison $parent
     */
    public function setParent($parent)
    {
        $this->parent = $parent;
    }

    /**
     * Get the parent Livraison.
     *
     * @return Livraison
     */
    public function getParent()
    {
        return $this->parent;
    }
}
