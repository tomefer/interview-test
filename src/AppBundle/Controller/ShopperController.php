<?php

namespace AppBundle\Controller;

use AppBundle\Entity\ClientOrderLine;
use AppBundle\Entity\Shop;
use AppBundle\Entity\Shopper;
use AppBundle\Entity\ShopperAssignment;
use AppBundle\Repository\ClientOrderLineRepository;
use AppBundle\Repository\ShopperAssignmentRepository;
use AppBundle\Repository\ShopRepository;
use AppBundle\Services\ShopperAssignmentService;
use Psr\Log\LoggerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;


class ShopperController extends Controller
{
    /**
     * @Route("/shopper/{id}/assignments", methods={"GET"})
     * @ParamConverter("shopper", class="AppBundle:Shopper")
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function shopperAssignmentsAction(Shopper $shopper, Request $request)
    {
        $shopId = $request->get('shopId', null);
        $shop = null;
        if (null !== $shopId) {
            /** @var ShopRepository $shopRepository */
            $shopRepository = $this->get(ShopRepository::class);
            $shop = $shopRepository->find($shopId);
            if (null === $shop) {
                throw new NotFoundHttpException('Shop not found');
            }
        }
        /** @var ShopperAssignmentService $shopperAssignmentService */
        $shopperAssignmentService = $this->get(ShopperAssignmentService::class);

        $response = $shopperAssignmentService->getShopperAssignments($shopper, $shop);

        return new JsonResponse($response);
    }

}
