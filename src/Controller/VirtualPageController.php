<?php

namespace App\Controller;

use App\Entity\VirtualPage;
use App\Enum\UserRolesEnum;
use App\Request;
use App\Service\Serializer\EntitySerializer;
use App\Service\Entity\VirtualPageService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use App\Interfaces\Auth\AuthenticationOAuth2Interface;
use App\Interfaces\Auth\AuthorizationRbacInterface;
use App\Util\Authentication\OAuth2AndRbacControllerTrait;
use App\Util\Authorization\RbacControllerTrait;

/**
 * Class DemoController
 * @package App\Controller
 *
 * @Route("/page", name="page_")
 */
class VirtualPageController extends BaseController implements AuthenticationOAuth2Interface, AuthorizationRbacInterface
{
    use RbacControllerTrait;
    use OAuth2AndRbacControllerTrait;

    /**
     * @return string
     */
    public static function getValidationFormsGroupName() : string
    {
        return 'VirtualPage';
    }

    /**
     * @return array|string[]
     */
    public static function getPermissions() : array
    {
        return [
            'create' => [UserRolesEnum::ADMIN],
            'delete' => [UserRolesEnum::ADMIN],
            'update' => [UserRolesEnum::ADMIN],
        ];
    }
    /**
     * @Route("/", name="create", methods={"POST"})
     *
     * @param Request $request
     * @param VirtualPageService $service
     * @return JsonResponse
     */
    public function create(Request $request, VirtualPageService $service)
    {
        $entity = $service->createEntityFromRequest($request);
        return $this->prepareResponse($entity->getId());
    }

    /**
     * @Route("/{id}", requirements={"id": "\d+"}, name="get_one", methods={"GET"})
     *
     * @param Request $request
     * @param VirtualPageService $service
     * @param EntitySerializer $serializer
     * @return JsonResponse
     */
    public function getOne(Request $request, VirtualPageService $service, EntitySerializer $serializer)
    {
        $entity = $service->getEntity($request->get('id'));
        if (is_null($entity)) {
            return $this->prepareErrorResponse('Страница не найдена');
        }

        $result = $serializer->serialize($entity);
        return $this->prepareResponse($result);
    }

    /**
     * @Route("/list", name="get_list", methods={"GET"})
     *
     * @param Request $request
     * @param VirtualPageService $service
     * @param EntitySerializer $serializer
     * @return JsonResponse
     */
    public function getList(Request $request, VirtualPageService $service, EntitySerializer $serializer)
    {
        $page = $service->getEntitiesPageFromRequest($request);
        $page->setSerializer(function(VirtualPage $entity) use ($serializer) {
            $result = $serializer->serialize($entity);
            $parent = $entity->getParentVirtualPage();
            $result['parentVirtualPage'] = $parent ? $parent->getId() : null;
            return $result;
        });
        $result = $page->toArray();
        return $this->prepareResponse($result);
    }

    /**
     * @Route("/{id}", requirements={"id": "\d+"}, name="update", methods={"PATCH"})
     *
     * @param VirtualPageService $service
     * @param Request $request
     * @return JsonResponse
     */
    public function update(Request $request, VirtualPageService $service)
    {
        $entity = $service->updateEntityFromRequest($request);
        return $this->prepareResponse($entity->getId());
    }

    /**
     * @Route("/{id}", requirements={"id": "\d+"}, name="delete", methods={"DELETE"})
     *
     * @param Request $request
     * @param VirtualPageService $service
     * @return JsonResponse
     * @throws \App\Exception\EntityNotFoundException
     */
    public function delete(Request $request, VirtualPageService $service)
    {
        $service->deleteEntityFromRequest($request);
        return $this->prepareResponse('Витруальная страница удалена');
    }

    /**
     * @Route("/menu", name="get_menu", methods={"GET"})
     *
     * @param VirtualPageService $service
     * @return JsonResponse
     */
    public function getMenu(VirtualPageService $service)
    {
        return $this->prepareResponse($service->getMenu());
    }
}
