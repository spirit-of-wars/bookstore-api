<?php

namespace App\Controller;

use App\Enum\UserRolesEnum;
use \App\Interfaces\Auth\AuthenticationOAuth2Interface;
use \App\Interfaces\Auth\AuthorizationRbacInterface;
use App\Request;
use App\Service\Entity\TagService;
use App\Service\Serializer\EntitySerializer;
use App\Util\Authentication\OAuth2AndRbacControllerTrait;
use App\Util\Authorization\RbacControllerTrait;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/tag", name="tag_")
 *
 * Class TagController
 * @package App\Controller
 */
class TagController extends BaseController implements AuthenticationOAuth2Interface, AuthorizationRbacInterface
{
    use OAuth2AndRbacControllerTrait;
    use RbacControllerTrait;

    /**
     * @return string
     */
    public static function getValidationFormsGroupName(): string
    {
        return 'Tag';
    }

    /**
     * @return array
     */
    public static function getPermissions() : array
    {
        return [
            'create' => [UserRolesEnum::ADMIN, UserRolesEnum::SUPER_ADMIN],
            'getOne' => [UserRolesEnum::ADMIN, UserRolesEnum::SUPER_ADMIN],
            'getList' => [UserRolesEnum::ADMIN, UserRolesEnum::SUPER_ADMIN],
            'update' => [UserRolesEnum::ADMIN, UserRolesEnum::SUPER_ADMIN],
            'delete' => [UserRolesEnum::ADMIN, UserRolesEnum::SUPER_ADMIN],
        ];
    }

    /**
     * @Route("/", name="create", methods={"POST"})
     *
     * @param Request $request
     * @param TagService $service
     * @return JsonResponse
     */
    public function create(Request $request, TagService $service)
    {
        $entity = $service->createEntityFromRequest($request);
        return $this->prepareResponse($entity->getId());
    }

    /**
     * @Route("/{id}", requirements={"id": "\d+"}, name="get_one", methods={"GET"})
     *
     * @param Request $request
     * @param TagService $service
     * @param EntitySerializer $serializer
     * @return JsonResponse
     */
    public function getOne(Request $request, TagService $service, EntitySerializer $serializer)
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
     * @param TagService $service
     * @return JsonResponse
     */
    public function getList(Request $request, TagService $service)
    {
        $page = $service->getEntitiesPageFromRequest($request);
        $result = $page->toArray();
        return $this->prepareResponse($result);
    }

    /**
     * @Route("/{id}", name="update", methods={"PATCH"})
     *
     * @param Request $request
     * @param TagService $service
     * @return JsonResponse
     */
    public function update(Request $request, TagService $service)
    {
        $entity = $service->updateEntityFromRequest($request);
        return $this->prepareResponse($entity->getId());
    }

    /**
     * @Route("/{id}", name="delete", methods={"DELETE"})
     *
     * @param Request $request
     * @param TagService $service
     * @return JsonResponse
     */
    public function delete(Request $request, TagService $service)
    {
        $service->deleteEntityFromRequest($request);
        return $this->prepareResponse('Тэг удален');
    }
}
