<?php

/**
 * Created by PhpStorm.
 * User: ftome
 * Date: 03/03/18
 * Time: 20:57
 */
namespace AppBundle\Services;

use AppBundle\Entity\Client;
use AppBundle\Entity\ClientAddress;
use AppBundle\Entity\ClientOrder;
use AppBundle\Entity\ClientOrderLine;
use AppBundle\Entity\Product;
use AppBundle\Repository\ClientAddressRepository;
use AppBundle\Repository\ProductRepository;
use AppBundle\Requests\ClientOrderRequest;
use Doctrine\ORM\EntityManagerInterface;
use Money\Currency;
use Money\Money;
use Psr\Log\LoggerInterface;

class ClientOrderService
{
    protected $clientAddressRepository;
    protected $entityManager;
    protected $productRepository;
    /** @var  LoggerInterface */
    protected $logger;



    public function __construct(ClientAddressRepository $clientAddressRepository,
                                EntityManagerInterface $entityManager,
                                ProductRepository $productRepository)
    {
        $this->clientAddressRepository = $clientAddressRepository;
        $this->entityManager = $entityManager;
        $this->productRepository = $productRepository;
    }

    /**
     * @required
     */
    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @param ClientOrderRequest $clientOrderRequest
     * @param Client $client
     * @return ClientOrder
     * @throws \Exception
     */
    public function createClientOrder(ClientOrderRequest $clientOrderRequest, Client $client)
    {
        $errors = $clientOrderRequest->validate();

        if (0 !== count($errors)) {
            throw new \InvalidArgumentException(implode(', ', $errors));
        }
        $address = $this->getClientAddress($clientOrderRequest->getAddressId(), $client);

        $this->entityManager->beginTransaction();
        try {
            $this->logger->info('Creating new ClientOrder', ['clientOrderRequest' => $clientOrderRequest->getRawData()]);
            $newOrder = new ClientOrder();

            $newOrder
                ->setAddress($address)
                ->setSelectedDeliveryDate($clientOrderRequest->getSelectedDeliveryDate())
                ->setStatus(ClientOrder::PLACED)
                ->setPurchaseDate(new \DateTime())
                ->setClient($client);


            $orderLines = $this->saveOrderLines($clientOrderRequest, $newOrder);
            $totalOrderPrice = $this->calculateOrderPrice($orderLines);
            $newOrder->setPrice($totalOrderPrice);

            $this->entityManager->persist($newOrder);
            $this->entityManager->flush();
            $this->entityManager->commit();
            $this->logger->info('Client Order created successfully', ['order' => $newOrder->getId()]);

            /*
             *  Aqui lo suyo es lanzar un evento y los listeners adecuados realicen las acciones necesarias:
             *  - Alerta en Slack
             *  - Email al cliente
             *  - Email al equipo de logistica
             *  - .. etc
             *  $event = new ClientOrderPlacedEvent($order);
             *  $dispatcher->dispatch(ClientOrderPlacedEvent::NEW_ORDER, $event);
             */


        } catch (\Exception $e) {
            $this->entityManager->rollback();
            $this->logger->info('Exception creating new ClientOrder', ['exception' => $e->getMessage()]);
            $this->logger->info('Error creating new ClientOrder', ['clientOrderRequest' => $clientOrderRequest->getRawData()]);
            // Si consideramos que una exception creando un pedido es importante (entiendo que si)
            // Aqui lanzaríamos otros eventos con el dispatcher para alertar a quien corresponda
            throw new \Exception('Error creating new ClientOrder');
        }
        return $newOrder;
    }

    private function getClientAddress($addressId, Client $client) : ClientAddress
    {
        /** @var ClientAddress $address */
        $address = $this->clientAddressRepository->findAddressForClient($addressId, $client);
        if (null === $address) {
            throw new \InvalidArgumentException('Address Id not found');
        }
        return $address;
    }

    private function saveOrderLines(ClientOrderRequest $clientOrderRequest, ClientOrder $newOrder) : array
    {
        $linesData = $clientOrderRequest->getClientOrderLines();
        $lines = [];
        foreach ($linesData as $lineData) {
            $productId = $lineData['productId'];
            /** @var Product $product */
            $product = $this->productRepository->find($productId);
            if (null === $product) {
                throw new \InvalidArgumentException('Product not found');
            }
            $line = new ClientOrderLine();
            $line
                ->setClientOrder($newOrder)
                ->setQuantity($lineData['quantity'])
                ->setProduct($product)
                ->setPrice(new Money($product->getPrice(), new Currency('EUR')))
            ;

            $this->entityManager->persist($line);
            $lines[] = $line;
        }
        return $lines;
    }

    /**
     * @param ClientOrderLine[] $orderLines
     * @return Money
     * @throws \InvalidArgumentException
     */
    private function calculateOrderPrice(array $orderLines) : Money
    {
        $totalPrice = new Money('0', new Currency('EUR'));
        foreach ($orderLines as $line) {
            $linePrice = (new Money($line->getPrice(), new Currency('EUR')))->multiply($line->getQuantity());
            $totalPrice = $totalPrice->add($linePrice);
        }
        return $totalPrice;
    }

}