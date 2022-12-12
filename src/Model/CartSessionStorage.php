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
use App\Repository\TransactionManagement\PurchaseRepository;
use App\Entity\TransactionManagement\Product;
use Symfony\Component\HttpFoundation\Session\SessionInterface;


/**
 * Class CartSessionStorage
 * @package App\Model
 */
class CartSessionStorage
{
    /**
     * The session storage.
     *
     * @var SessionInterface
     */
    private $session;

    /**
     * The cart repository.
     *
     * @var PurchaseRepository
     */
    private $cartRepository;

    /**
     * @var string
     */
    const CART_KEY_NAME = 'cart_id';

    /**
     * CartSessionStorage constructor.
     *
     * @param SessionInterface $session
     * @param PurchaseRepository $cartRepository
     */
    public function __construct(SessionInterface $session, PurchaseRepository $cartRepository) 
    {
        $this->session = $session;
        $this->cartRepository = $cartRepository;
    }

    /**
     * Gets the cart in session.
     *
     * @return Purchase|null
     */
    public function getCart(): ?Purchase
    {
        return $this->cartRepository->findOneBy([
            'id' => $this->getCartId(),
            'status' => Purchase::STATUS_CART
        ]);
    }

    /**
     * Sets the cart in session.
     *
     * @param Purchase $cart
     */
    public function setCart(Purchase $cart): void
    {
        $this->session->set(self::CART_KEY_NAME, $cart->getId());
    }

    /**
     * Returns the cart id.
     *
     * return int|null
     */
    private function getCartId() //: ?int
    {
        return $this->session->get(self::CART_KEY_NAME);
    }
}
