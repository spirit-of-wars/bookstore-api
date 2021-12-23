<?php

namespace App\Controller;

use App\Service\ReferenceService;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Request;
use Symfony\Component\Routing\Annotation\Route;
use Exception;

/**
 * @Route("/reference", name="reference_")
 */
class ReferenceController extends BaseController
{
    /**
     * @return string
     */
    public static function getValidationFormsGroupName() : string
    {
        return 'Reference';
    }

    /**
     * @Route("/", name="get_all", methods={"GET"})
     * @param Request $request
     * @param ReferenceService $referenceService
     * @return JsonResponse
     */
    public function getAll(Request $request, ReferenceService $referenceService)
    {
        try {
            $types = $request->get('types') ?? [];
            $references = $referenceService->getReferencesByFilter($types);
            return $this->prepareResponse($references);
        } catch (Exception $e) {
            return $this->prepareErrorResponse([
                'code' => 462,
                'parameter' => 'types',
                'message' => 'Неверные ключи справочника'
            ]);
        }
    }
}
