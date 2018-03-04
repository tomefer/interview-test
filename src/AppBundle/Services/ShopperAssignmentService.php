<?php
/**
 * Created by PhpStorm.
 * User: ftome
 * Date: 04/03/18
 * Time: 12:49
 */

namespace AppBundle\Services;


use AppBundle\Entity\ClientOrderLine;
use AppBundle\Entity\Shop;
use AppBundle\Entity\Shopper;
use AppBundle\Entity\ShopperAssignment;
use AppBundle\Repository\ShopperAssignmentRepository;
use Psr\Log\LoggerInterface;

class ShopperAssignmentService
{

    protected $shopperAssignmentRepository;
    /** @var  LoggerInterface */
    protected $logger;

    public function __construct(ShopperAssignmentRepository $shopperAssignmentRepository)
    {
        $this->shopperAssignmentRepository = $shopperAssignmentRepository;
    }

    /**
     * @required
     * @param LoggerInterface $logger
     */
    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     *  Con esta query se puede sacar, si quisiesemos mejorar la performance lo haría así primero, (no soy muy fan del entity manager)
     *  después cachearía en memcached, aunque cachear una asignación implicaría descachear cada vez que se modifique la
     *  lista de asignaciones, y eso en general aumenta la complejidad (a veces no hay mas remedio que hacerlo)
     *  select *
     *   from client_order co
     *   join client_order_line col on col.client_order_id = co.id
     *   join product p on col.product_id = p.id
     *   join shopper_assignment sa on sa.client_order_id = co.id
     *   where shopper_assignment.shop_id = :shopId AND shopper_assignment.shopper_id = :shopperId
     *
     * @param Shopper $shopper
     * @param Shop|null $shop
     * @return array
     */
    public function getShopperAssignments(Shopper $shopper, Shop $shop = null): array
    {

        $assignmentFilter = ['shopper' => $shopper];
        if (null !== $shop) {
            $assignmentFilter['shop'] = $shop->getId();
        }

        /** @var ShopperAssignment[] $assignments */
        $assignments = $this->shopperAssignmentRepository->findBy($assignmentFilter);

        $this->logger->debug('Found ' . count($assignments) .' assignments for shopper ' . $shopper->getId() ?? null . 'and shop ' . $shop->getId());

        $response = [];
        foreach ($assignments as $assignment) {
            $clientOrder = $assignment->getClientOrder();
            if (null === $shop) {
                $lines = $clientOrder->getClientOrderLines();
            } else {
                $lines = $clientOrder->getClientOrderLines()->filter(function(ClientOrderLine $foundLine) use ($shop) {
                    return $foundLine->getProduct()->getShop()->getId() === $shop->getId();
                });
            }
            $linesResponse = $this->formatLinesResponse($lines);
            $response[] = ['clientOrder' => [
                'id' => $clientOrder->getId(),
                'totalPrice' => $clientOrder->getPrice(),
                'selectedDeliveryDate' => $clientOrder->getSelectedDeliveryDate(),
                'purchaseDate' => $clientOrder->getPurchaseDate(),
                'orderLines' => $linesResponse
            ]];
        }
        return $response;
    }

    /**
     * @param ClientOrderLine[] $lines
     * @return array
     */
    protected function formatLinesResponse($lines): array
    {
        $linesResponse = [];
        foreach ($lines as $line) {
            $linesResponse[] = [
                'clientOrderLineid' => $line->getId(),
                'quantity' => $line->getQuantity(),
                'price' => $line->getPrice(),
                'productData' => $line->getProduct(),
                'shop' => $line->getProduct()->getShop()->getName()
            ];
        }
        return $linesResponse;
    }


}