<?php

namespace App\Controller;

use App\Entity\ProductGroup\Category;
use App\Enum\UserRolesEnum;
use App\Interfaces\Auth\AuthenticationOAuth2Interface;
use App\Interfaces\Auth\AuthorizationRbacInterface;
use App\Service\Entity\CategoryService;
use App\Service\Serializer\EntitySerializer;
use App\Util\Authentication\OAuth2AndRbacControllerTrait;
use App\Util\Authorization\RbacControllerTrait;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Request;

/**
 * @Route("category/", name="category_")
 *
 * Class CategoryController
 * @package App\Controller
 */
class CategoryController extends BaseController implements AuthenticationOAuth2Interface, AuthorizationRbacInterface
{
    use RbacControllerTrait;
    use OAuth2AndRbacControllerTrait;

    /**
     * @return array|string[]
     */
    public static function getPermissions() : array
    {
        return [
            'create' => [UserRolesEnum::ADMIN],
            'update' => [UserRolesEnum::ADMIN],
            'delete' => [UserRolesEnum::ADMIN],
        ];
    }
    /**
     * @return string
     */
    public static function getValidationFormsGroupName() : string
    {
        return 'Category';
    }

    /**
     * @Route("", name="create", methods={"POST"})
     *
     * @param Request $request
     * @param CategoryService $service
     * @return JsonResponse
     */
    public function create(Request $request, CategoryService $service): JsonResponse
    {
        $entity = $service->createEntityFromRequest($request);
        return $this->prepareResponse($entity->getId());
    }

    /**
     * @Route("{id}", requirements={"id": "\d+"}, name="get_one", methods={"GET"})
     *
     * @param Request $request
     * @param CategoryService $service
     * @param EntitySerializer $serializer
     * @return JsonResponse
     */
    public function getOne(Request $request, CategoryService $service, EntitySerializer $serializer) : JsonResponse
    {
        /** @var Category $entity */
        $entity = $service->getEntity($request->get('id'));
        if (is_null($entity)) {
            return $this->prepareErrorResponse('Категория не найдена');
        }

        $result = $serializer->serialize($entity);
        $parent = $entity->getParentCategory();
        $result['parentCategory'] = $parent ? $parent->getId() : null;
        return $this->prepareResponse($result);
    }

    /**
     * @Route("list", name="get_list", methods={"GET"})
     *
     * @param Request $request
     * @param CategoryService $service
     * @param EntitySerializer $serializer
     * @return JsonResponse
     */
    public function getList(Request $request, CategoryService $service, EntitySerializer $serializer) : JsonResponse
    {
        $page = $service->getEntitiesPageFromRequest($request);
        $page->setSerializer(function(Category $entity) use ($serializer) {
            $result = $serializer->serialize($entity);
            $parent = $entity->getParentCategory();
            $result['parentCategory'] = $parent ? $parent->getId() : null;
            return $result;
        });
        $result = $page->toArray();
        return $this->prepareResponse($result);
    }

    /**
     * @Route("{id}", requirements={"id": "\d+"}, name="update", methods={"PATCH"})
     *
     * @param Request $request
     * @param CategoryService $service
     * @return JsonResponse
     */
    public function update(Request $request, CategoryService $service) : JsonResponse
    {
        $entity = $service->updateEntityFromRequest($request);
        return $this->prepareResponse($entity->getId());
    }

    /**
     * @Route("{id}", requirements={"id": "\d+"}, name="delete", methods={"DELETE"})
     *
     * @param Request $request
     * @param CategoryService $service
     * @return JsonResponse
     */
    public function delete(Request $request, CategoryService $service) : JsonResponse
    {
        $service->deleteEntityFromRequest($request);
        return $this->prepareResponse('Категория удалена');
    }
}
