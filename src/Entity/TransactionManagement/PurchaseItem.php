<?php


namespace App\Entity\TransactionManagement;

use Doctrine\ORM\Mapping as ORM;
use DH\DoctrineAuditBundle\Annotation as Audit;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class PurchaseItem.
 *
 * @ORM\HasLifecycleCallbacks()
 * @Audit\Auditable
 * @Audit\Security(view={"ROLE_ADMIN"}) 
 * @ORM\Table(name="purchase_item")
 * @ORM\Entity
 */
class PurchaseItem
{
    /**
     * The identifier of the image.
     *
     * @var int
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id = null;

    /**
     * The ordered quantity.
     *
     * @var int
     * @ORM\Column(type="smallint")
     * @Assert\NotBlank()
     * @Assert\GreaterThanOrEqual(1)
     */
    protected $quantity = 1;

    // *
    //  * The tax rate to apply on the product.
    //  *
    //  * @var string
    //  * @ORM\Column(type="decimal", name="tax_rate")
     
    // protected $taxRate = 1;

    /**
     * The ordered product.
     *
     * @var Product
     * @ORM\ManyToOne(targetEntity="Product", inversedBy="purchasedItems")
     * @ORM\JoinColumn(name="product_id", referencedColumnName="id")
     */
    protected $product;

    /**
     * @ORM\ManyToOne(targetEntity="Purchase", inversedBy="purchasedItems", cascade={"persist"})
     * @ORM\JoinColumn(name="purchase_id", referencedColumnName="id")
     */
    protected $purchase;

    /**
     * @ORM\ManyToOne(targetEntity=Livraison::class, inversedBy="purchasedItems")
     */
    private $livraison;

    /**
     * @param Product $product
     */
    public function setProduct($product)
    {
        $this->product = $product;
    }

    /**
     * @return Product
     */
    public function getProduct()
    {
        return $this->product;
    }

    /**
     * @param Purchase $purchase
     */
    public function setPurchase($purchase)
    {
        $this->purchase = $purchase;
    }

    /**
     * @return Purchase
     */
    public function getPurchase()
    {
        return $this->purchase;
    }

    /**
     * @param int $quantity
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;
    }

    /**
     * @return int
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    // /**
    //  * @param string $taxRate
    //  */
    // public function setTaxRate($taxRate)
    // {
    //     $this->taxRate = $taxRate;
    // }

    // /**
    //  * @return string
    //  */
    // public function getTaxRate()
    // {
    //     return $this->taxRate;
    // }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /** {@inheritdoc} */
    public function __toString()
    {
        return $this->getProduct()->getName().' [x'.$this->getQuantity().']: '.$this->getTotalPrice();
    }

    /**
     * Return the total price (tax included).
     *
     * @return float
     */
    public function getTotalPrice()
    {
        return $this->product->getPrice() * $this->quantity;
        //return $this->product->getPrice() * $this->quantity * (1 + $this->taxRate);
    }

    public function getLivraison(): ?Livraison
    {
        return $this->livraison;
    }

    public function setLivraison(?Livraison $livraison): self
    {
        $this->livraison = $livraison;

        return $this;
    }

    /**
     * Tests if the given item given corresponds to the same order item.
     *
     * @param PurchaseItem $item
     *
     * @return bool
     */
    public function equals(PurchaseItem $item): bool
    {
        return $this->getProduct()->getId() === $item->getProduct()->getId();
    }
}
