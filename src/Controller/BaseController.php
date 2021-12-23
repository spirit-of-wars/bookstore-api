<?php

namespace App\Controller;

use App\Interfaces\ErrorResponseProviderInterface;
use App\MifTools\ErrorResponseProvider;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Class BaseController
 * @package App\Controller
 */
abstract class BaseController extends AbstractController
{
    /**
     * @return string
     */
    abstract public static function getValidationFormsGroupName() : string;

    /**
     * @return array
     */
    public static function getActionsWithoutValidation() : array
    {
        return [];
    }

    /**
     * @param string $nameAction
     * @return bool
     */
    public function ignoreValidationForm(string $nameAction) : bool
    {
        if (in_array($nameAction, static::getActionsWithoutValidation())) {
            return true;
        }

        return false;
    }

    /**
     * @param mixed $data
     * @return JsonResponse
     */
    protected function prepareResponse($data) : JsonResponse
    {
        if ($data instanceof ErrorResponseProviderInterface) {
            return $data->getErrorResponse();
        }

        return new JsonResponse([
            'success' => true,
            'data' => $data,
        ]);
    }

    /**
     * @param mixed $message
     * @return JsonResponse
     */
    protected function prepareErrorResponse($message) : JsonResponse
    {
        return $this->prepareResponse(new ErrorResponseProvider($message));
    }
}
