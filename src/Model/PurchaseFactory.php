<?php

/*
 * This file is part of the Doctrine-TestSet project created by
 * https://github.com/MacFJA
 *
 * For the full copyright and license information, please view the LICENSE
 * at https://github.com/MacFJA/Doctrine-TestSet
 */

namespace App\Model;

use App\Entity\TransactionManagement\Purchase;
use App\Entity\TransactionManagement\PurchaseItem;
use App\Entity\TransactionManagement\Product;

/**
 * Class PurchaseFactory.
 * Define a simple PHP class.
 *
 */
class PurchaseFactory
{
    /**
     * Creates an purchase.
     *
     * @return Purchase
     */
    public function create(): Purchase
    {
        $purchase = new Purchase();
        $purchase
            ->setStatus(Purchase::STATUS_CART)
            ->setUpdatedAt(new \DateTime());

        return $purchase;
    }

    /**
     * Creates an item for a product.
     *
     * @param Product $product
     *
     * @return PurchaseItem
     */
    public function createItem(Product $product): PurchaseItem
    {
        $item = new PurchaseItem();
        $item->setProduct($product);
        $item->setQuantity(1);

        return $item;
    }
}
