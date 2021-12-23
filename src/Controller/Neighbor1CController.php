<?php

namespace App\Controller;

use App\Interfaces\Auth\AuthorizationNeighborInterface;
use App\Mif;
use App\Service\Data1CSynchronizationService;
use App\Util\Authorization\AuthConstant;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/1c", name="1c_")
 */
class Neighbor1CController extends BaseController implements AuthorizationNeighborInterface
{
    /**
     * @return string
     */
    public static function getValidationFormsGroupName() : string
    {
        return 'ApiFor1C';
    }

    /**
     * @param string $action
     * @return string
     */
    public function getAuthSecret(string $action) : string
    {
        return Mif::getEnvConfig('SECRET_KEY_FOR_1C');
    }

    /**
     * @Route("/sync-data1c", name="save_packet_data1c", methods={"POST"})
     * @param LoggerInterface $logger
     * @param Data1CSynchronizationService $service
     * @param Request $request
     * @return JsonResponse
     */
    public function syncData1c(LoggerInterface $logger, Data1CSynchronizationService $service, Request $request)
    {
        $data = $request->get(AuthConstant::NEIGHBOR_DATA_PARAMETER);

        $result = $service->synchronize($data);
        if (!is_null($result)) {
            $logger->error('error synchronize data: '. $result);
            return $this->prepareErrorResponse('error synchronize data');
        }

        return $this->prepareResponse('ok');
    }
}
