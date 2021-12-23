<?php

namespace App\Controller;

use App\Enum\UserRolesEnum;
use App\Service\Entity\FactoidService;
use App\Service\Serializer\EntitySerializer;
use App\Util\Authentication\OAuth2AndRbacControllerTrait;
use App\Util\Authorization\RbacControllerTrait;
use \App\Interfaces\Auth\AuthenticationOAuth2Interface;
use \App\Interfaces\Auth\AuthorizationRbacInterface;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/factoid", name="factoid_")
 *
 * Class FactoidController
 * @package App\Controller
 */
class FactoidController extends BaseController implements AuthenticationOAuth2Interface, AuthorizationRbacInterface
{
    use RbacControllerTrait;
    use OAuth2AndRbacControllerTrait;

    /**
     * @return string
     */
    public static function getValidationFormsGroupName(): string
    {
        return 'Factoid';
    }

    /**
     * @return array|string[]
     */
    public static function getPermissions() : array
    {
        return [
            'create' => [UserRolesEnum::SUPER_ADMIN, UserRolesEnum::ADMIN],
            'update' => [UserRolesEnum::SUPER_ADMIN, UserRolesEnum::ADMIN],
            'delete' => [UserRolesEnum::SUPER_ADMIN, UserRolesEnum::ADMIN],
        ];
    }

    /**
     * @Route("/", name="create", methods={"POST"})
     *
     * @param Request $request
     * @param FactoidService $service
     * @return JsonResponse
     */
    public function create(Request $request, FactoidService $service)
    {
        $entity = $service->createEntityFromRequest($request);
        return $this->prepareResponse($entity->getId());
    }

    /**
     * @Route("/list", name="get_list", methods={"GET"})
     *
     * @param Request $request
     * @param FactoidService $service
     * @return JsonResponse
     */
    public function getList(Request $request, FactoidService $service)
    {
        $page = $service->getEntitiesPageFromRequest($request);
        $result = $page->toArray();
        return $this->prepareResponse($result);
    }

    /**
     * @Route("/{id}", requirements={"id": "\d+"}, name="get_one", methods={"GET"})
     *
     * @param Request $request
     * @param FactoidService $service
     * @param EntitySerializer $serializer
     * @return JsonResponse
     */
    public function getOne(Request $request, FactoidService $service, EntitySerializer $serializer)
    {
        $entity = $service->getEntity($request->get('id'));
        if (is_null($entity)) {
            return $this->prepareErrorResponse('Фактоид не найден');
        }

        $result = $serializer->serialize($entity);
        return $this->prepareResponse($result);
    }

    /**
     * @Route("/{id}", name="update", methods={"PATCH"})
     *
     * @param Request $request
     * @param FactoidService $service
     * @return JsonResponse
     */
    public function update(Request $request, FactoidService $service)
    {
        $entity = $service->updateEntityFromRequest($request);
        return $this->prepareResponse($entity->getId());
    }

    /**
     * @Route("/{id}", name="delete", methods={"DELETE"})
     *
     * @param Request $request
     * @param FactoidService $service
     * @return JsonResponse
     */
    public function delete(Request $request, FactoidService $service)
    {
        $service->deleteEntityFromRequest($request);
        return $this->prepareResponse('Фактоид удален');
    }
}
