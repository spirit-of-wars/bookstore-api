<?php

namespace App\Controller;

use App\Enum\UserRolesEnum;
use App\Interfaces\Auth\AuthenticationOAuth2Interface;
use App\Interfaces\Auth\AuthorizationRbacInterface;
use App\Service\Entity\PromoTagService;
use App\Service\Serializer\EntitySerializer;
use App\Util\Authentication\OAuth2AndRbacControllerTrait;
use App\Util\Authorization\RbacControllerTrait;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/promo-tag", name="promo_tag_")
 *
 * Class PromoTagController
 * @package App\Controller
 */
class PromoTagController extends BaseController implements AuthenticationOAuth2Interface, AuthorizationRbacInterface
{
    use OAuth2AndRbacControllerTrait;
    use RbacControllerTrait;

    /**
     * @return string
     */
    public static function getValidationFormsGroupName(): string
    {
        return 'PromoTag';
    }

    /**
     * @return array
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
     * @param PromoTagService $service
     * @return JsonResponse
     */
    public function create(Request $request, PromoTagService $service)
    {
        $entity = $service->createEntityFromRequest($request);
        return $this->prepareResponse($entity->getId());
    }

    /**
     * @Route("/{id}", requirements={"id": "\d+"}, name="get_one", methods={"GET"})
     *
     * @param Request $request
     * @param PromoTagService $service
     * @param EntitySerializer $serializer
     * @return JsonResponse
     */
    public function getOne(Request $request, PromoTagService $service, EntitySerializer $serializer)
    {
        $entity = $service->getEntity($request->get('id'));
        if (is_null($entity)) {
            return $this->prepareErrorResponse('Автор не найден');
        }

        $result = $serializer->serialize($entity);
        return $this->prepareResponse($result);
    }

    /**
     * @Route("/list", name="get_list", methods={"GET"})
     *
     * @param Request $request
     * @param PromoTagService $service
     * @return JsonResponse
     */
    public function getList(Request $request, PromoTagService $service)
    {
        $page = $service->getEntitiesPageFromRequest($request);
        $result = $page->toArray();
        return $this->prepareResponse($result);
    }

    /**
     * @Route ("/{id}", requirements={"id": "\d+"}, name="update", methods={"PATCH"})
     *
     * @param Request $request
     * @param PromoTagService $service
     * @return JsonResponse
     */
    public function update(Request $request, PromoTagService $service)
    {
        $entity = $service->updateEntityFromRequest($request);
        return $this->prepareResponse($entity->getId());
    }

    /**
     * @Route("/{id}", name="delete", methods={"DELETE"})
     *
     * @param Request $request
     * @param PromoTagService $service
     * @return JsonResponse
     */
    public function delete(Request $request, PromoTagService $service)
    {
        $service->deleteEntityFromRequest($request);
        return $this->prepareResponse('Промотэг удален');
    }
}
