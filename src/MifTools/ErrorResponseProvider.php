<?php

namespace App\MifTools;

use App\Interfaces\ErrorResponseProviderInterface;
use App\Util\Common\ErrorsCollectorTrait;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class ResponseError
 * @package App\MifTools
 */
class ErrorResponseProvider implements ErrorResponseProviderInterface
{
    use ErrorsCollectorTrait;

    /** @var integer */
    private $responseCode;

    /**
     * ErrorResponseProvider constructor.
     * @param string|array $message
     * @param int $code
     */
    public function __construct($message = null, $code = Response::HTTP_BAD_REQUEST)
    {
        $this->responseCode = $code;

        if (is_array($message)) {
            $this->errors[] = $message;
        } else {
            if ($message) {
                $this->addError($message);
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
            'errorCode' => $this->responseCode,
            'errorDetails' => $this->getErrors(),
        ], $this->responseCode);
    }
}
