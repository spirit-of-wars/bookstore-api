<?php

namespace App\Controller;

use App\Enum\UserRolesEnum;
use App\Interfaces\Auth\AuthenticationOAuth2Interface;
use App\Interfaces\Auth\AuthorizationRbacInterface;
use App\Service\Entity\BannerService;
use App\Service\Serializer\EntitySerializer;
use App\Util\Authentication\OAuth2AndRbacControllerTrait;
use App\Util\Authorization\RbacControllerTrait;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/banner", name="banner_")
 *
 * Class BannerController
 * @package App\Controller
 */
class BannerController extends BaseController implements AuthenticationOAuth2Interface, AuthorizationRbacInterface
{
    use RbacControllerTrait;
    use OAuth2AndRbacControllerTrait;

    /**
     * @return string
     */
    public static function getValidationFormsGroupName() : string
    {
        return 'Banner';
    }

    /**
     * @return array|string[]
     */
    public static function getPermissions() : array
    {
        return [
            'createMini' => [UserRolesEnum::SUPER_ADMIN, UserRolesEnum::ADMIN],
            'createBig' => [UserRolesEnum::SUPER_ADMIN, UserRolesEnum::ADMIN],
            'updateMini' => [UserRolesEnum::SUPER_ADMIN, UserRolesEnum::ADMIN],
            'updateBig' => [UserRolesEnum::SUPER_ADMIN, UserRolesEnum::ADMIN],
            'delete' => [UserRolesEnum::SUPER_ADMIN, UserRolesEnum::ADMIN],
        ];
    }

    /**
     * @Route("/mini", name="create_mini_banner", methods={"POST"})
     *
     * @param Request $request
     * @param BannerService $service
     * @return JsonResponse
     */
    public function createMini(Request $request, BannerService $service)
    {
        $entity = $service->createMiniBannerFromRequest($request);
        return $this->prepareResponse($entity->getId());
    }

    /**
     * @Route("/big", name="create_big_banner", methods={"POST"})
     *
     * @param Request $request
     * @param BannerService $service
     * @return JsonResponse
     */
    public function createBig(Request $request, BannerService $service)
    {
        $entity = $service->createBigBannerFromRequest($request);
        return $this->prepareResponse($entity->getId());
    }

    /**
     * @Route("/{id}", requirements={"id": "\d+"}, name="get_one", methods={"GET"})
     *
     * @param Request $request
     * @param BannerService $service
     * @param EntitySerializer $serializer
     * @return JsonResponse
     */
    public function getOne(Request $request, BannerService $service, EntitySerializer $serializer)
    {
        $entity = $service->getEntity($request->get('id'));
        if (is_null($entity)) {
            return $this->prepareErrorResponse('Баннер не найден');
        }

        $result = $serializer->serialize($entity);
        return $this->prepareResponse($result);
    }

    /**
     * @Route("/list", name="get_list", methods={"GET"})
     *
     * @param Request $request
     * @param BannerService $service
     * @return JsonResponse
     */
    public function getList(Request $request, BannerService $service)
    {
        $page = $service->getEntitiesPageFromRequest($request);
        $result = $page->toArray();
        return $this->prepareResponse($result);
    }

    /**
     * @Route("/mini/{id}", name="update_mini", methods={"PATCH"})
     *
     * @param Request $request
     * @param BannerService $service
     * @return JsonResponse
     * @throws \App\Exception\BadRequestException
     * @throws \App\Exception\EntityNotFoundException
     */
    public function updateMini(Request $request, BannerService $service)
    {
        $entity = $service->updateMini($request);
        return $this->prepareResponse($entity->getId());
    }

    /**
     * @Route("/big/{id}", name="update_big", methods={"PATCH"})
     *
     * @param Request $request
     * @param BannerService $service
     * @return JsonResponse
     * @throws \App\Exception\BadRequestException
     * @throws \App\Exception\EntityNotFoundException
     */
    public function updateBig(Request $request, BannerService $service)
    {
        $entity = $service->updateBig($request);
        return $this->prepareResponse($entity->getId());
    }

    /**
     * @Route("/{id}", name="delete", methods={"DELETE"})
     *
     * @param Request $request
     * @param BannerService $service
     * @return JsonResponse
     */
    public function delete(Request $request, BannerService $service)
    {
        $service->deleteEntityFromRequest($request);
        return $this->prepareResponse('Баннер удален');
    }
}
