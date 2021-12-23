<?php

namespace App\Controller;

use App\Enum\UserRolesEnum;
use App\Interfaces\Auth\AuthenticationOAuth2Interface;
use App\Interfaces\Auth\AuthorizationRbacInterface;
use App\Service\Entity\CreativePartnerService;
use App\Service\Serializer\EntitySerializer;
use App\Util\Authentication\OAuth2AndRbacControllerTrait;
use App\Util\Authorization\RbacControllerTrait;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Request;

/**
 * @Route("/creative-partner", name="creative_partner_")
 *
 * Class CreativePartnerController
 * @package App\Controller
 */
class CreativePartnerController extends BaseController implements AuthenticationOAuth2Interface, AuthorizationRbacInterface
{
    use RbacControllerTrait;
    use OAuth2AndRbacControllerTrait;

    /**
     * @return string
     */
    public static function getValidationFormsGroupName() : string
    {
        return 'CreativePartner';
    }

    /**
     * @return array|string[]
     */
    public static function getPermissions() : array
    {
        return [
            'create' => [UserRolesEnum::ADMIN, UserRolesEnum::SUPER_ADMIN],
            'update' => [UserRolesEnum::ADMIN, UserRolesEnum::SUPER_ADMIN],
            'delete' => [UserRolesEnum::ADMIN, UserRolesEnum::SUPER_ADMIN],
        ];
    }

    /**
     * @Route("/", name="create", methods={"POST"})
     *
     * @param Request $request
     * @param CreativePartnerService $service
     * @return JsonResponse
     */
    public function create(Request $request, CreativePartnerService $service): JsonResponse
    {
        $entity = $service->createEntityFromRequest($request);
        return $this->prepareResponse($entity->getId());
    }

    /**
     * @Route("/{id}", requirements={"id": "\d+"}, name="get_one", methods={"GET"})
     *
     * @param Request $request
     * @param CreativePartnerService $service
     * @param EntitySerializer $serializer
     * @return JsonResponse
     */
    public function getOne(Request $request, CreativePartnerService $service, EntitySerializer $serializer)
    {
        $entity = $service->getEntity($request->get('id'));
        if (is_null($entity)) {
            return $this->prepareErrorResponse('Партнёр не найден');
        }

        $result = $serializer->serialize($entity);
        return $this->prepareResponse($result);
    }

    /**
     * @Route("/list", name="get_list", methods={"GET"})
     *
     * @param Request $request
     * @param CreativePartnerService $service
     * @return JsonResponse
     */
    public function getList(Request $request, CreativePartnerService $service) : JsonResponse
    {
        $page = $service->getEntitiesPageFromRequest($request);
        $result = $page->toArray();
        return $this->prepareResponse($result);
    }

    /**
     * @Route("/{id}", requirements={"id": "\d+"}, name="update", methods={"PATCH"})
     *
     * @param Request $request
     * @param CreativePartnerService $service
     * @return JsonResponse
     */
    public function update(Request $request, CreativePartnerService $service) : JsonResponse
    {
        $entity = $service->updateEntityFromRequest($request);
        return $this->prepareResponse($entity->getId());
    }

    /**
     * @Route("/{id}", requirements={"id": "\d+"}, name="delete", methods={"DELETE"})
     *
     * @param Request $request
     * @param CreativePartnerService $service
     * @return JsonResponse
     */
    public function delete(Request $request, CreativePartnerService $service) : JsonResponse
    {
        $service->deleteEntityFromRequest($request);
        return $this->prepareResponse('Партнер удален');
    }
}
