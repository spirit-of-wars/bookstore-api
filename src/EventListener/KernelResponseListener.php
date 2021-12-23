<?php

namespace App\EventListener;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ResponseEvent;

/**
 * Class KernelResponseListener
 * @package App\EventListener
 */
class KernelResponseListener
{
    public function onKernelResponse(ResponseEvent $event)
    {
        $response = $event->getResponse();
        if ($response->getStatusCode() == Response::HTTP_NOT_FOUND) {
            $event->setResponse(new JsonResponse([
                'success' => false,
                'errorCode' => Response::HTTP_NOT_FOUND,
                'errorDetails' => [
                    'Ресурс не найден'
                ],
            ], Response::HTTP_NOT_FOUND));
        }
    }
}
