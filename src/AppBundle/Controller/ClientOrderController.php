<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Client;
use AppBundle\Repository\ClientRepository;
use AppBundle\Requests\ClientOrderRequest;
use AppBundle\Services\ClientOrderService;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class ClientOrderController extends Controller
{


    /**
     * @Route("/clientOrder",methods={"POST"})
     * @throws \LogicException
     */
    public function createOrder(Request $request)
    {
        $client = $this->getUser();
        $clientOrderRequestData = json_decode($request->getContent(), true);
        $clientOrderRequest = new ClientOrderRequest($clientOrderRequestData);
        /** @var ClientOrderService $clientOrderService */
        $clientOrderService = $this->get(ClientOrderService::class);
        $result = $clientOrderService->createClientOrder($clientOrderRequest, $client);
        return new JsonResponse(['success' => true, 'data' => $result]);
    }

    /**
     * Getting the same user always. Not implementing a UserProvicer etc...
     * The api will provide an access token an we will use it to fetch the user from
     * cache or the database
     */
    public function getUser()
    {
        /** @var ClientRepository $clientRepository */
        $clientRepository = $this->get(ClientRepository::class);
        return $clientRepository->findOneBy(['email' => 'fernando@example.com']);
    }

}
