<?php
namespace AppBundle\Requests;


/**
 * Created by PhpStorm.
 * User: ftome
 * Date: 03/03/18
 * Time: 20:58
 */
class ClientOrderRequest
{
    protected $clientOrderLines = [];
    protected $addressId;
    protected $shopId;
    protected $selectedDeliveryDate;
    protected $rawData;
    protected $errors = [];

    public function __construct(array $requestData)
    {
        $this->rawData = $requestData;
        $this->shopId = $requestData['clientOrder']['shopId'] ?? null;
        $this->addressId = $requestData['clientOrder']['clientAddress'] ?? null;
        $date = $requestData['clientOrder']['selectedDeliveryDate'] ?? null;
        $this->selectedDeliveryDate = \DateTime::createFromFormat('Y-m-d H:i:s', $date);
        $this->clientOrderLines = $requestData['clientOrder']['lines'] ?? null;
    }

    /**
     * @return array
     */
    public function getClientOrderLines(): array
    {
        return $this->clientOrderLines;
    }

    /**
     * @param array $clientOrderLines
     * @return ClientOrderRequest
     */
    public function setClientOrderLines(array $clientOrderLines): ClientOrderRequest
    {
        $this->clientOrderLines = $clientOrderLines;
        return $this;
    }

    /**
     * @return mixed|null
     */
    public function getAddressId()
    {
        return $this->addressId;
    }

    /**
     * @param mixed|null $addressId
     * @return ClientOrderRequest
     */
    public function setAddressId($addressId)
    {
        $this->addressId = $addressId;
        return $this;
    }

    /**
     * @return mixed|null
     */
    public function getShopId()
    {
        return $this->shopId;
    }

    /**
     * @param mixed|null $shopId
     * @return ClientOrderRequest
     */
    public function setShopId($shopId)
    {
        $this->shopId = $shopId;
        return $this;
    }

    /**
     * @return mixed|null
     */
    public function getSelectedDeliveryDate()
    {
        return $this->selectedDeliveryDate;
    }

    /**
     * @param mixed|null $selectedDeliveryDate
     * @return ClientOrderRequest
     */
    public function setSelectedDeliveryDate($selectedDeliveryDate)
    {
        $this->selectedDeliveryDate = $selectedDeliveryDate;
        return $this;
    }

    public function getRawData()
    {
        return $this->rawData;
    }


    /**
     * TODO: Use a proper validator
     */
    public function validate()
    {
        if (false === $this->selectedDeliveryDate) {
            $this->errors[] = 'selectedDeliveryDate should be a valid date';
        }
        if (null === $this->addressId && is_int($this->addressId)) {
            $this->errors[] = 'addressId should be an integer';
        }

        if (null === $this->shopId && is_int($this->shopId)) {
            $this->errors[] = 'shopId should be an integer';
        }

        if (0 === count($this->clientOrderLines)) {
            $this->errors[] = 'The order must contain lines';
        }

        foreach ($this->clientOrderLines as $line) {
            if (!array_key_exists('productId', $line) && !is_int($line['productId'])) {
                $this->errors[] = 'productId should be an integer';
            }
            if (!array_key_exists('quantity', $line) && !is_int($line['quantity'])) {
                $this->errors[] = 'quantity should be an integer';
            }
        }
        return $this->errors;
    }
}