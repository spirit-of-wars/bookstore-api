<?php

namespace App\Controller;

use App\Enum\UserRolesEnum;
use App\Interfaces\Auth\AuthenticationOAuth2Interface;
use App\Interfaces\Auth\AuthorizationRbacInterface;
use App\Util\Authentication\OAuth2AndRbacControllerTrait;
use App\Util\Authorization\RbacControllerTrait;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Request;
use App\Service\Serializer\EntitySerializer;
use App\Service\Entity\AuthorService;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/author", name="author_")
 *
 * Class AuthorController
 * @package App\Controller
 */
class AuthorController extends BaseController implements AuthenticationOAuth2Interface, AuthorizationRbacInterface
{
    use OAuth2AndRbacControllerTrait;
    use RbacControllerTrait;

    /**
     * @inheritDoc
     */
    public static function getValidationFormsGroupName(): string
    {
        return 'Author';
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
     * @param AuthorService $service
     * @return JsonResponse
     */
    public function create(Request $request, AuthorService $service)
    {
        $entity = $service->createEntityFromRequest($request);
        return $this->prepareResponse($entity->getId());
    }

    /**
     * @Route("/{id}", requirements={"id": "\d+"}, name="get_one", methods={"GET"})
     *
     * @param Request $request
     * @param AuthorService $service
     * @param EntitySerializer $serializer
     * @return JsonResponse
     */
    public function getOne(Request $request, AuthorService $service, EntitySerializer $serializer)
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
     * @param AuthorService $service
     * @return JsonResponse
     */
    public function getList(Request $request, AuthorService $service)
    {
        $page = $service->getEntitiesPageFromRequest($request);
        $result = $page->toArray();
        return $this->prepareResponse($result);
    }

    /**
     * @Route("/{id}", requirements={"id": "\d+"}, name="update", methods={"PATCH"})
     *
     * @param Request $request
     * @param AuthorService $service
     * @return JsonResponse
     */
    public function update(Request $request, AuthorService $service)
    {
        $entity = $service->updateEntityFromRequest($request);
        return $this->prepareResponse($entity->getId());
    }

    /**
     * @Route("/{id}", name="delete", methods={"DELETE"})
     *
     * @param Request $request
     * @param AuthorService $service
     * @return JsonResponse
     */
    public function delete(Request $request, AuthorService $service)
    {
        $service->deleteEntityFromRequest($request);
        return $this->prepareResponse('Автор удален');
    }
}
