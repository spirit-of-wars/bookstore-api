<?php

namespace App\Controller;

use App\Interfaces\Auth\AuthorizationNeighborInterface;
use App\Mif;
use App\Request;
use App\Util\ExceptionProcessor\ExceptionDataLoader;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class ExceptionController
 * @package App\Controller
 *
 * @Route("/exception", name="exception_")
 */
class ExceptionController extends BaseController implements AuthorizationNeighborInterface
{
    /**
     * @return string
     */
    public static function getValidationFormsGroupName() : string
    {
        return 'Exception';
    }

    /**
     * @param string $action
     * @return string
     */
    public function getAuthSecret(string $action) : string
    {
        return Mif::getEnvConfig('SECRET_KEY_FOR_EXCEPTION');
    }

    /**
     * @Route("/get-by-code", name="get_by_code", methods={"GET"})
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getException(Request $request)
    {
        $code = $request->get('code');
        $date = $request->get('date');

        $loader = new ExceptionDataLoader();
        $result = $loader->load($code, $date);

        return $this->prepareResponse($result);
    }
}
