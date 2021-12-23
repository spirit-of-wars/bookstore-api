<?php

use App\Mif;
use App\Request;
use App\Exception\MifException;
use Symfony\Component\ErrorHandler\Debug;
use Symfony\Component\HttpFoundation\JsonResponse;

require dirname(__DIR__).'/config/bootstrap.php';

if ($_SERVER['APP_DEBUG']) {
    umask(0000);

    Debug::enable();
}

if ($trustedProxies = $_SERVER['TRUSTED_PROXIES'] ?? $_ENV['TRUSTED_PROXIES'] ?? false) {
    Request::setTrustedProxies(explode(',', $trustedProxies), Request::HEADER_X_FORWARDED_ALL ^ Request::HEADER_X_FORWARDED_HOST);
}

if ($trustedHosts = $_SERVER['TRUSTED_HOSTS'] ?? $_ENV['TRUSTED_HOSTS'] ?? false) {
    Request::setTrustedHosts([$trustedHosts]);
}

try {
    $request = Request::createFromGlobals();
} catch (Exception $exception) {
    if ($exception instanceof MifException) {
        $response = new JsonResponse([
            'success' => false,
            'errorCode' => $exception->getCode(),
            'errorDetails' => $exception->getMessage(),
        ]);
        $response->setStatusCode($exception->getCode());
        $response->send();
        exit();
    } else {
        throw $exception;
    }
}

$kernel = Mif::defineApplication();
$response = $kernel->handle($request);
$response->send();
$kernel->terminate($request, $response);
