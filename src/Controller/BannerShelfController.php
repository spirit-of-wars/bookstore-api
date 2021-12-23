<?php

namespace App\Controller;

use App\Enum\UserRolesEnum;
use App\Interfaces\Auth\AuthenticationOAuth2Interface;
use App\Interfaces\Auth\AuthorizationRbacInterface;
use App\Service\Entity\BannerShelfService;
use App\Service\Serializer\EntitySerializer;
use App\Util\Authentication\OAuth2AndRbacControllerTrait;
use App\Util\Authorization\RbacControllerTrait;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/banner-shelf", name="banner_shelf_")
 *
 * Class BannerShelfController
 * @package App\Controller
 */
class BannerShelfController extends BaseController implements AuthenticationOAuth2Interface, AuthorizationRbacInterface
{
    use RbacControllerTrait;
    use OAuth2AndRbacControllerTrait;

    /**
     * @return string
     */
    public static function getValidationFormsGroupName() : string
    {
        return 'BannerShelf';
    }

    /**
     * @return array|string[]
     */
    public static function getPermissions() : array
    {
        return [
            'create' => [UserRolesEnum::SUPER_ADMIN, UserRolesEnum::ADMIN],
            'getList' => [UserRolesEnum::SUPER_ADMIN, UserRolesEnum::ADMIN],
            'update' => [UserRolesEnum::SUPER_ADMIN, UserRolesEnum::ADMIN],
            'delete' => [UserRolesEnum::SUPER_ADMIN, UserRolesEnum::ADMIN],
        ];
    }

    /**
     * @Route("/", name="create", methods={"POST"})
     *
     * @param Request $request
     * @param BannerShelfService $service
     * @return JsonResponse
     */
    public function create(Request $request, BannerShelfService $service)
    {
        $entity = $service->createEntityFromRequest($request);
        return $this->prepareResponse($entity->getId());
    }

    /**
     * @Route("/{id}", requirements={"id": "\d+"}, name="get_one", methods={"GET"})
     *
     * @param Request $request
     * @param BannerShelfService $service
     * @param EntitySerializer $serializer
     * @return JsonResponse
     */
    public function getOne(Request $request, BannerShelfService $service, EntitySerializer $serializer)
    {
        $entity = $service->getEntity($request->get('id'));
        if (is_null($entity)) {
            return $this->prepareErrorResponse('Полка не найдена');
        }

        $result = $serializer->serialize($entity);
        return $this->prepareResponse($result);
    }

    /**
     * @Route("/list", name="get_list", methods={"GET"})
     *
     * @param Request $request
     * @param BannerShelfService $service
     * @return JsonResponse
     */
    public function getList(Request $request, BannerShelfService $service)
    {
        $page = $service->getEntitiesPageFromRequest($request);
        $result = $page->toArray();
        return $this->prepareResponse($result);
    }

    /**
     * @Route("/{id}", requirements={"id": "\d+"}, name="update", methods={"PATCH"})
     *
     * @param Request $request
     * @param BannerShelfService $service
     * @return JsonResponse
     */
    public function update(Request $request, BannerShelfService $service)
    {
        $entity = $service->updateEntityFromRequest($request);
        return $this->prepareResponse($entity->getId());
    }

    /**
     * @Route("/{id}", name="delete", methods={"DELETE"})
     *
     * @param Request $request
     * @param BannerShelfService $service
     * @return JsonResponse
     */
    public function delete(Request $request, BannerShelfService $service)
    {
        $service->deleteEntityFromRequest($request);
        return $this->prepareResponse('Полка удалена');
    }
}
