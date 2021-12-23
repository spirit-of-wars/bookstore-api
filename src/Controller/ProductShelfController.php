<?php

namespace App\Controller;

use App\Enum\UserRolesEnum;
use App\Exception\BadRequestException;
use App\Interfaces\Auth\AuthenticationOAuth2Interface;
use App\Interfaces\Auth\AuthorizationRbacInterface;
use App\Service\Entity\ProductShelfService;
use App\Service\Serializer\EntitySerializer;
use App\Util\Authentication\OAuth2AndRbacControllerTrait;
use App\Util\Authorization\RbacControllerTrait;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/product-shelf", name="product_shelf_")
 *
 * Class ProductShelfController
 * @package App\Controller
 */
class ProductShelfController extends BaseController implements AuthenticationOAuth2Interface, AuthorizationRbacInterface
{
    use RbacControllerTrait;
    use OAuth2AndRbacControllerTrait;

    /**
     * @return string
     */
    public static function getValidationFormsGroupName(): string
    {
        return 'ProductShelf';
    }

    /**
     * @return array|string[]
     */
    public static function getPermissions() : array
    {
        return [
            'create' => [UserRolesEnum::SUPER_ADMIN, UserRolesEnum::ADMIN],
            'getOne' => [UserRolesEnum::SUPER_ADMIN, UserRolesEnum::ADMIN],
            'getList' => [UserRolesEnum::SUPER_ADMIN, UserRolesEnum::ADMIN],
            'update' => [UserRolesEnum::SUPER_ADMIN, UserRolesEnum::ADMIN],
            'delete' => [UserRolesEnum::SUPER_ADMIN, UserRolesEnum::ADMIN],
        ];
    }

    /**
     * @Route("/", name="create", methods={"POST"})
     *
     * @param Request $request
     * @param ProductShelfService $service
     * @return JsonResponse
     * @throws BadRequestException
     */
    public function create(Request $request, ProductShelfService $service)
    {
        $entity = $service->createEntityFromRequest($request);
        return $this->prepareResponse($entity->getId());
    }

    /**
     * @Route("/{id}", requirements={"id": "\d+"}, name="get_one", methods={"GET"})
     *
     * @param Request $request
     * @param ProductShelfService $service
     * @param EntitySerializer $serializer
     * @return JsonResponse
     */
    public function getOne(Request $request, ProductShelfService $service, EntitySerializer $serializer)
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
     * @param ProductShelfService $service
     * @return JsonResponse
     */
    public function getList(Request $request, ProductShelfService $service)
    {
        $page = $service->getEntitiesPageFromRequest($request);
        $result = $page->toArray();
        return $this->prepareResponse($result);
    }

    /**
     * @Route("/{id}", requirements={"id": "\d+"}, name="update", methods={"PATCH"})
     *
     * @param Request $request
     * @param ProductShelfService $service
     * @return JsonResponse
     */
    public function update(Request $request, ProductShelfService $service)
    {
        $entity = $service->updateEntityFromRequest($request);
        return $this->prepareResponse($entity->getId());
    }

    /**
     * @Route("/{id}", requirements={"id": "\d+"}, name="delete", methods={"DELETE"})
     *
     * @param Request $request
     * @param ProductShelfService $service
     * @return JsonResponse
     */
    public function delete(Request $request, ProductShelfService $service)
    {
        $service->deleteEntityFromRequest($request);
        return $this->prepareResponse('Полка удалена');
    }

    /**
     * @Route("/get-products/{code}", name="get_products", methods={"GET"})
     *
     * @param Request $request
     * @param ProductShelfService $productShelfService
     * @return JsonResponse
     * @throws NonUniqueResultException
     */
    public function getProducts(Request $request, ProductShelfService $productShelfService)
    {
        //TODO тут надо всё актуализировать. Как минимум нет сериализации
        $result = $productShelfService->getProducts(
            $request->get('code'),
            $request->get('page'),
            $request->get('perPage')
        );

        if (is_null($result)) {
            return $this->prepareErrorResponse('undefined shelf');
        }

        return $this->prepareResponse($result);
    }
}
