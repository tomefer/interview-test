<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Money\Money;

/**
 * ClientOrderLine
 *
 * @ORM\Table(name="client_order_line")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ClientOrderLineRepository")
 */
class ClientOrderLine
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var ClientOrder
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\ClientOrder", inversedBy="clientOrder")
     */
    private $clientOrder;

    /**
     * @var Product
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Product", inversedBy="product")
     */
    private $product;

    /**
     * @var int
     *
     * @ORM\Column(name="quantity", type="integer")
     */
    private $quantity;

    /**
     * @var string
     *
     * @ORM\Column(name="price", type="string", length=255)
     */
    private $price;

    /**
     * TODO: This promotion id is not implemented, it's here just to point out how design should be
     * @ORM\Column(name="promotionId", type="integer", nullable=true)
     */
    private $promotionId;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updatedAt", type="datetime", columnDefinition="DATETIME on update CURRENT_TIMESTAMP", nullable=true)
     */
    private $updatedAt;


    public function __construct()
    {
        $this->updatedAt = new \DateTime();
    }

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return Product
     */
    public function getProduct()
    {
        return $this->product;
    }

    /**
     * @param Product $product
     * @return ClientOrderLine
     */
    public function setProduct(Product $product)
    {
        $this->product = $product;
        return $this;
    }

    /**
     * @return ClientOrder
     */
    public function getClientOrder()
    {
        return $this->clientOrder;
    }

    /**
     * @param ClientOrder $clientOrder
     * @return ClientOrderLine
     */
    public function setClientOrder(ClientOrder $clientOrder)
    {
        $this->clientOrder = $clientOrder;
        return $this;
    }



    /**
     * Set quantity
     *
     * @param integer $quantity
     *
     * @return ClientOrderLine
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;

        return $this;
    }

    /**
     * Get quantity
     *
     * @return int
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * Set price
     *
     * @param string $price
     *
     * @return ClientOrderLine
     */
    public function setPrice(Money $price)
    {
        $this->price = $price->getAmount();
        return $this;
    }

    /**
     * Get price
     *
     * @return string
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * Set promotionId
     *
     * @param integer $promotionId
     *
     * @return ClientOrderLine
     */
    public function setPromotionId($promotionId)
    {
        $this->promotionId = $promotionId;

        return $this;
    }

    /**
     * Get promotionId
     *
     * @return int
     */
    public function getPromotionId()
    {
        return $this->promotionId;
    }

    /**
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     *
     * @return ClientOrderLine
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Get updatedAt
     *
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }
}

