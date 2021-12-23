<?php

namespace App\Controller;

use App\Enum\UserRolesEnum;
use App\Interfaces\Auth\AuthenticationOAuth2Interface;
use App\Interfaces\Auth\AuthorizationRbacInterface;
use App\Request;
use App\Service\Entity\SeriesService;
use App\Service\Serializer\EntitySerializer;
use App\Util\Authentication\OAuth2AndRbacControllerTrait;
use App\Util\Authorization\RbacControllerTrait;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/series", name="series_")
 *
 * Class SeriesController
 * @package App\Controller
 */
class SeriesController extends BaseController implements AuthenticationOAuth2Interface, AuthorizationRbacInterface
{
    use OAuth2AndRbacControllerTrait;
    use RbacControllerTrait;

    /**
     * @return string
     */
    public static function getValidationFormsGroupName(): string
    {
        return 'Series';
    }

    /**
     * @return array
     */
    public static function getPermissions(): array
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
     * @Route("/", name="create_series", methods={"POST"})
     *
     * @param Request $request
     * @param SeriesService $service
     * @return JsonResponse
     */
    public function create(Request $request, SeriesService $service)
    {
        $entity = $service->createEntityFromRequest($request);
        return $this->prepareResponse($entity->getId());
    }

    /**
     * @Route("/{id}", requirements={"id": "\d+"}, name="get_one", methods={"GET"})
     *
     * @param Request $request
     * @param SeriesService $service
     * @param EntitySerializer $serializer
     * @return JsonResponse
     */
    public function getOne(Request $request, SeriesService $service, EntitySerializer $serializer)
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
     * @param SeriesService $service
     * @return JsonResponse
     */
    public function getList(Request $request, SeriesService $service)
    {
        $page = $service->getEntitiesPageFromRequest($request);
        $result = $page->toArray();
        return $this->prepareResponse($result);
    }

    /**
     * @Route("/{id}", name="update", methods={"PATCH"})
     *
     * @param Request $request
     * @param SeriesService $service
     * @return JsonResponse
     */
    public function update(Request $request, SeriesService $service)
    {
        $entity = $service->updateEntityFromRequest($request);
        return $this->prepareResponse($entity->getId());
    }

    /**
     * @Route("/{id}", name="delete", methods={"DELETE"})
     *
     * @param Request $request
     * @param SeriesService $service
     * @return JsonResponse
     */
    public function delete(Request $request, SeriesService $service)
    {
        $service->deleteEntityFromRequest($request);
        return $this->prepareResponse('Серия удалена');
    }
}
