<?php

namespace App\EventListener;

use App\Exception\MifException;
use App\Mif;
use App\Util\ExceptionProcessor\ExceptionProcessor;
use DateTime;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;

/**
 * Class KernelExceptionListener
 * @package App\EventListener
 */
class KernelExceptionListener
{
    public function onKernelException(ExceptionEvent $event)
    {
        $exception = $event->getThrowable();
        if ($exception instanceof MifException) {
            $exception->process();

            $response = new JsonResponse([
                'success' => false,
                'errorCode' => $exception->getCode(),
                'errorDetails' => $exception->getMessage(),
            ]);
            $response->setStatusCode($exception->getCode());

            $event->setResponse($response);
        } else {
            $processor = new ExceptionProcessor();
            $date = (new DateTime())->format('Y-m-d');
            $processor->setDate($date);
            $processor->setRequest(Mif::$app->getRequest());
            $code = $processor->process($exception);
            $message = "Произошла внутренняя ошибка сервера. Сообщите администратору или команде разработки дату: '$date' и код ошибки: '$code'";

            $response = new JsonResponse([
                'success' => false,
                'errorCode' => Response::HTTP_INTERNAL_SERVER_ERROR,
                'errorDetails' => $message,
            ]);
            $response->setStatusCode(Response::HTTP_INTERNAL_SERVER_ERROR);

            $event->setResponse($response);
        }
    }
}
