<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Money\Money;

/**
 * ClientOrder
 *
 * @ORM\Table(name="client_order")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ClientOrderRepository")
 */
class ClientOrder implements \JsonSerializable
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
     * @var Client
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Client", inversedBy="client")
     */
    private $client;

    /**
     * @var ClientAddress
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\ClientAddress", inversedBy="clientAddress")
     */
    private $address;

    /**
     * @var string
     *
     * @ORM\Column(name="price", type="string", length=255)
     */
    private $price;

    /**
     * @var Collection | ShopperAssignment[]
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\ShopperAssignment", mappedBy="shopperAssignment")
     */
    private $shopperAssignments;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="purchaseDate", type="datetime")
     */
    private $purchaseDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="selectedDeliveryDate", type="datetime")
     */
    private $selectedDeliveryDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="deliveredAt", type="datetime", nullable=true)
     */
    private $deliveredAt;

    /**
     * @var int
     *
     * @ORM\Column(name="status", type="integer")
     */
    private $status;
    /**
     * @var Collection | ClientOrderLine[]
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\ClientOrderLine", mappedBy="clientOrder")
     */
    private $clientOrderLines;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updatedAt", type="datetime", columnDefinition="DATETIME on update CURRENT_TIMESTAMP", nullable=true)
     */
    private $updatedAt;

    const PLACED = 1;
    const WAITING_SHOPPER_CONFIRMATION = 2;
    const SHOPPER_BUYING = 3;
    const SHOPPER_DELIVERING = 4;
    const DELIVERED = 5;
    const CANCELLED = 6;

    public function __construct()
    {
        $this->shopperAssignments = new ArrayCollection();
        $this->clientOrderLines = new ArrayCollection();
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
     * @return Client
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * @param Client $client
     * @return ClientOrder
     */
    public function setClient(Client $client)
    {
        $this->client = $client;
        return $this;
    }

    /**
     * @return ClientAddress
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * @param ClientAddress $address
     * @return ClientOrder
     */
    public function setAddress(ClientAddress $address)
    {
        $this->address = $address;
        return $this;
    }

    /**
     * Set price
     *
     * @param string $price
     *
     * @return ClientOrder
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
     * Set purchaseDate
     *
     * @param \DateTime $purchaseDate
     *
     * @return ClientOrder
     */
    public function setPurchaseDate($purchaseDate)
    {
        $this->purchaseDate = $purchaseDate;

        return $this;
    }

    /**
     * Get purchaseDate
     *
     * @return \DateTime
     */
    public function getPurchaseDate()
    {
        return $this->purchaseDate;
    }

    /**
     * Set selectedDeliveryDate
     *
     * @param \DateTime $selectedDeliveryDate
     *
     * @return ClientOrder
     */
    public function setSelectedDeliveryDate($selectedDeliveryDate)
    {
        $this->selectedDeliveryDate = $selectedDeliveryDate;

        return $this;
    }

    /**
     * Get selectedDeliveryDate
     *
     * @return \DateTime
     */
    public function getSelectedDeliveryDate()
    {
        return $this->selectedDeliveryDate;
    }

    /**
     * Set deliveredAt
     *
     * @param \DateTime $deliveredAt
     *
     * @return ClientOrder
     */
    public function setDeliveredAt($deliveredAt)
    {
        $this->deliveredAt = $deliveredAt;

        return $this;
    }

    /**
     * Get deliveredAt
     *
     * @return \DateTime
     */
    public function getDeliveredAt()
    {
        return $this->deliveredAt;
    }

    /**
     * Set status
     *
     * @param integer $status
     *
     * @return ClientOrder
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
     * @return ClientOrder
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

    public function getShopperAssignments()
    {
        return $this->shopperAssignments;
    }

    /**
     * @return ClientOrderLine[]|Collection
     */
    public function getClientOrderLines()
    {
        return $this->clientOrderLines;
    }

    public function jsonSerialize()
    {
        $objectProperties = get_object_vars($this);
        return $objectProperties;
    }
}

