<?php


namespace App\Entity\TransactionManagement;

use Doctrine\Common\Collections\ArrayCollection;
use DH\DoctrineAuditBundle\Annotation as Audit;
use App\Model\Shipment;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\UserManagement\User;
use App\Entity\LovManagement\Etat;

/**
 * Class Purchase.
 *
 * @ORM\HasLifecycleCallbacks()
 * @Audit\Auditable
 * @Audit\Security(view={"ROLE_ADMIN"}) 
 * @ORM\Table(name="purchase")
 * @ORM\Entity
 */
class Purchase
{
    /**
     * The purchase increment id. This identifier will be use in all communication between the user and the store.
     *
     * @var string
     * @ORM\Column(type="string", name="id", nullable=false)
     * @ORM\Id
     */
    protected $id = null;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $status = self::STATUS_CART;

    /**
     * An order that is in progress, not placed yet.
     *
     * @var string
     */
    const STATUS_CART = 'cart';

    /**
     * The date of the delivery (it doesn't include the time).
     *
     * var \DateTime
     * ORM\Column(type="date")
     */
    // protected $deliveryDate = null;

    /**
     * The purchase datetime in the user timezone.
     *
     * @var \DateTime
     * @ORM\Column(type="datetimetz")
     */
    protected $createdAt = null;

    /**
     * @ORM\Column(type="datetime")
     */
    private $updatedAt;

    /**
     * The shipping information.
     *
     * @var Shipment
     * @ORM\Column(type="object")
     */
    protected $shipping = null;
    
    /**
     * The user preferred time of the day for the delivery.
     *
     * var \DateTime|null
     * ORM\Column(type="time", nullable=true)
     */
    // protected $deliveryHour = null;

    /**
     * The user billing address.
     *
     * @var array
     * @ORM\Column(type="text", nullable=true)
     */
    protected $billingAddress;

    /**
     * The user who made the purchase.
     *
     * @var User
     * @ORM\ManyToOne(targetEntity="App\Entity\UserManagement\User", inversedBy="purchases")
     */
    protected $buyer;

    /**
     * Items that have been purchased.
     *
     * @var PurchaseItem[]
     * @ORM\OneToMany(targetEntity="PurchaseItem", mappedBy="purchase", cascade={"persist"})
     */
    protected $purchasedItems;


    /**
     * @ORM\ManyToOne(targetEntity=Etat::class, inversedBy="livraisons")
     * @ORM\JoinColumn(nullable=true)
     */
    private $etat;

    /**
     * Constructor of the Purchase class.
     * (Initialize some fields).
     */
    public function __construct()
    {
        $this->id = $this->generateId();
        $this->purchasedItems = new ArrayCollection();
        $this->createdAt = new \DateTime();
        // $this->deliveryDate = new \DateTime('+2 days');
        // $this->deliveryHour = new \DateTime('14:00');
    }

    /**
     * Set the address where the user want its billing.
     *
     * @param string $billingAddress
     */
    public function setBillingAddress($billingAddress)
    {
        $this->billingAddress = $billingAddress;
    }

    /**
     * @return string
     */
    public function getBillingAddress()
    {
        return $this->billingAddress;
    }

    /**
     * @param User $buyer
     */
    public function setBuyer($buyer)
    {
        $this->buyer = $buyer;
    }

    /**
     * @return User
     */
    public function getBuyer()
    {
        return $this->buyer;
    }

    // /**
    //  * @param \DateTime $deliveryDate
    //  */
    // public function setDeliveryDate($deliveryDate)
    // {
    //     $this->deliveryDate = $deliveryDate;
    // }

    // /**
    //  * @return \DateTime
    //  */
    // public function getDeliveryDate()
    // {
    //     return $this->deliveryDate;
    // }

    /**
     * @param PurchaseItem[] $purchasedItems
     */
    public function setPurchasedItems($purchasedItems)
    {
        $this->purchasedItems = $purchasedItems;
    }

    /**
     * @return PurchaseItem[]
     */
    public function getPurchasedItems()
    {
        return $this->purchasedItems;
    }

    public function removePurchasedItems(PurchaseItem $purchasedItem): self
    {
        
        if ($this->purchasedItems->removeElement($purchasedItem)) {
            // set the owning side to null (unless already changed)
            if ($purchasedItem->getPurchase() === $this) {
                $purchasedItem->setPurchase(null);
            }
        }

        return $this;
    }

    /**
     * Removes all PurchasedItems from the order.
     *
     * @return $this
     */
    public function removePurchasedItemss(): self
    {
        foreach ($this->getPurchasedItems() as $item) {
            $this->removePurchasedItems($item);
        }

        return $this;
    }

    public function addPurchasedItems(PurchaseItem $purchasedItem): self
    {
        foreach ($this->getPurchasedItems() as $existingItem) {
            // The item already exists, update the quantity
            if ($existingItem->equals($purchasedItem)) {
                $existingItem->setQuantity(
                    $existingItem->getQuantity() + $purchasedItem->getQuantity()
                );
                return $this;
            }
        }

        $this->purchasedItems[] = $purchasedItem;
        $purchasedItem->setPurchase($this);

        return $this;
    }

    // /**
    //  * @param \DateTime $deliveryHour
    //  */
    // public function setDeliveryHour($deliveryHour)
    // {
    //     $this->deliveryHour = $deliveryHour;
    // }

    // /**
    //  * @return \DateTime|null
    //  */
    // public function getDeliveryHour()
    // {
    //     return $this->deliveryHour;
    // }

    /**
     * @param \DateTime $createdAt
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param Shipment $shipping
     */
    public function setShipping($shipping)
    {
        $this->shipping = $shipping;
    }

    /**
     * @return Shipment
     */
    public function getShipping()
    {
        return $this->shipping;
    }

     public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @param int $storeId
     *
     * @return string
     */
    public function generateId($storeId = 1)
    {
        return preg_replace('/[^0-9]/i', '', sprintf('%d%d%03d%s', $storeId, date('Y'), date('z'), microtime()));
    }

    /** {@inheritdoc} */
    public function __toString()
    {
        return 'Purchase #'.$this->getId();
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $id
     *
     * @return Purchase
     */
    public function setId($id)
    {
        $this->id = $id;

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

    public function getTotal()
    {
        $total = 0.0;

        foreach ($this->getPurchasedItems() as $item) {
            $total += $item->getTotalPrice();
        }

        return $total;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }


}
