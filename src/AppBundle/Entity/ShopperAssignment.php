<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints\DateTime;

/**
 * ShopperAssignment
 *
 * @ORM\Table(name="shopper_assignment")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ShopperAssignmentRepository")
 */
class ShopperAssignment
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
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\ClientOrder", inversedBy="clientOrder")
     */
    private $clientOrder;

    /**
     * @var Shop
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Shop", inversedBy="shop")
     */
    private $shop;

    /**
     * @var Shopper
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Shopper", inversedBy="shopper")
     */
    private $shopper;

    /**
     * @var int
     *
     * @ORM\Column(name="status", type="integer")
     */
    private $status;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updatedAt", type="datetime", columnDefinition="DATETIME on update CURRENT_TIMESTAMP", nullable=true)
     */
    private $updatedAt;

    const PROPOSAL = 0;
    const ASSIGNED = 1;
    const COMPLETED = 2;
    const CANCELLED = 3;

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
     * Set status
     *
     * @param integer $status
     *
     * @return ShopperAssignment
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return int
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     *
     * @return ShopperAssignment
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

    /**
     * @return ClientOrder
     */
    public function getClientOrder()
    {
        return $this->clientOrder;
    }

    /**
     * @param ClientOrder $clientOrder
     * @return ShopperAssignment
     */
    public function setClientOrder(ClientOrder $clientOrder)
    {
        $this->clientOrder = $clientOrder;
        return $this;
    }

    /**
     * @return Shop
     */
    public function getShop()
    {
        return $this->shop;
    }

    /**
     * @param Shop $shop
     * @return ShopperAssignment
     */
    public function setShop(Shop $shop)
    {
        $this->shop = $shop;
        return $this;
    }

    /**
     * @return Shopper
     */
    public function getShopper()
    {
        return $this->shopper;
    }

    /**
     * @param Shopper $shopper
     * @return ShopperAssignment
     */
    public function setShopper(Shopper $shopper)
    {
        $this->shopper = $shopper;
        return $this;
    }


}

