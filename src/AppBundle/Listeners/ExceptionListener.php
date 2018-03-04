<?php

/**
 * Created by PhpStorm.
 * User: ftome
 * Date: 04/03/18
 * Time: 00:30
 */
namespace AppBundle\Listeners;


use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ExceptionListener
{
    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        $exception = $event->getException();
        $response = new JsonResponse();
        $response->setStatusCode(400);


        if ($exception instanceof \InvalidArgumentException) {
            $response->setContent(json_encode([
                'success' => false,
                'errors' => $exception->getMessage()
            ]));
        } else if ($exception instanceof NotFoundHttpException) {
            $response->setContent(json_encode([
                'success' => false,
                'errors' => $exception->getMessage()
            ]));
            $response->setStatusCode(404);
        } else if ($exception instanceof \Exception) {
            $response->setContent(json_encode([
                'success' => false,
                'exception' => get_class($exception),
                'errors' => $exception->getMessage()
            ]));
        }
        $event->setResponse($response);
    }
}