<?php

namespace App\Util\Authorization;

use App\Interfaces\Auth\AuthorizationNeighborInterface;
use App\Interfaces\Auth\AuthorizationProcessorInterface;
use App\Util\Common\ErrorsCollectorTrait;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class AuthorizationNeighborProcessor
 * @package App\Util\Authentication
 */
class AuthorizationNeighborProcessor implements AuthorizationProcessorInterface
{
    use ErrorsCollectorTrait;

    /**
     * @param Request $request
     * @param AuthorizationNeighborInterface $controller
     * @param string $action
     */
    public function runAuthorization(Request $request, $controller, string $action)
    {
        $secret = $controller->getAuthSecret($action);
        if (!$secret) {

            return;
        }

        $authKey = $request->get(AuthConstant::NEIGHBOR_AUTHENTICATION_PARAMETER);
        if (!$authKey) {
            $this->addError('Ключ авторизации отсутствует');

            return;
        }

        $data = $request->get(AuthConstant::NEIGHBOR_DATA_PARAMETER);
        if ($data) {
            if (!$this->processWithData($data, $authKey, $secret)) {
                $this->addError('Ключ авторизации неверный');
            }
        } else {
            $publicKey = $request->get(AuthConstant::NEIGHBOR_PUBLIC_PARAMETER);
            if (!$publicKey) {
                $this->addError('Отсутствует публичный ключ');
                return;
            }

            if (!$this->processWithPublicKey($publicKey, $authKey, $secret)) {
                $this->addError('Неверный публичный ключ');
            }
        }
    }

    /**
     * @return JsonResponse
     */
    public function getErrorResponse() : JsonResponse
    {
        return new JsonResponse([
            'success' => false,
            'errorCode' => Response::HTTP_FORBIDDEN,
            'errorDetails' => $this->getErrors(),
        ], Response::HTTP_FORBIDDEN);
    }

    /**
     * @param string $publicKey
     * @param string $authKey
     * @param string $secret
     * @return bool
     */
    private function processWithPublicKey($publicKey, $authKey, $secret)
    {
        $hash = sha1($secret . $publicKey);
        return ($hash === $authKey);
    }

    /**
     * @param array $data
     * @param string $authKey
     * @param string $secret
     * @return bool
     */
    private function processWithData(array $data, string $authKey, string $secret) : bool
    {
        $arrKeys = $this->getKeyArray($data);
        sort($arrKeys);
        $key = sha1(implode($arrKeys) . $secret);

        return $key === $authKey;
    }

    /**
     * @param array $dataRequest
     * @param array $arrKeys
     * @return array
     */
    private function getKeyArray(array $dataRequest, &$arrKeys = []) : array
    {
        foreach ($dataRequest as $key => $value) {
            $arrKeys[] = (string)$key;
            if (is_array($value)) {
                $this->getKeyArray($value, $arrKeys);
            }
        }

        return $arrKeys;
    }
}
