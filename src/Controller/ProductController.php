<?php

namespace App\Controller;

use App\Model\ProductModelProvider;
use App\Enum\UserRolesEnum;
use App\Interfaces\Auth\AuthenticationOAuth2Interface;
use App\Interfaces\Auth\AuthorizationRbacInterface;
use App\Mif;
use App\Model\ProductModelSerializer;
use App\Service\File\FileService;
use App\Util\Authentication\OAuth2AndRbacControllerTrait;
use App\Util\Authorization\RbacControllerTrait;
use Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Entity\Product;
use App\Service\Entity\Product\CommonService as ProductCommonService;
use App\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/product", name="product_")
 *
 * Class ProductController
 * @package App\Controller
 */
class ProductController extends BaseController implements AuthenticationOAuth2Interface, AuthorizationRbacInterface
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
        return 'Product';
    }

    /**
     * @Route("/{type}", name="create", methods={"POST"})
     *
     * @param Request $request
     * @param ProductCommonService $service
     * @return JsonResponse
     * @throws Exception
     */
    public function create(Request $request, ProductCommonService $service): JsonResponse
    {
        $productModel = $service->createEntityFromRequest($request);

        return $this->prepareResponse([
            'id' => $productModel->getProductId(),
            'message' => 'Создан новый продукт',
        ]);
    }

    /**
     * @Route("/{id}", requirements={"id": "\d+"}, name="get_one", methods={"GET"})
     *
     * @param Request $request
     * @param ProductCommonService $service
     * @return JsonResponse
     */
    public function getOne(Request $request, ProductCommonService $service) : JsonResponse
    {
        $productModel = $service->getProductModel($request->get('id'));
        if (is_null($productModel)) {
            return $this->prepareErrorResponse('Продукт не найден');
        }

        $serializer = new ProductModelSerializer();
        $result = $serializer->serialize($productModel);
        return $this->prepareResponse($result);
    }

    /**
     * @Route("/list", name="get_list", methods={"GET"})
     *
     * @param Request $request
     * @param ProductCommonService $service
     * @return JsonResponse
     */
    public function getList(Request $request, ProductCommonService $service) : JsonResponse
    {
        $page = $service->getEntitiesPageFromRequest($request);

        $serializer = new ProductModelSerializer();
        $page->setSerializer(function(Product $product) use ($serializer) {
            $productModel = ProductModelProvider::getByProductId($product->getId());
            return $serializer->serialize($productModel);
        });
        $result = $page->toArray();

        return $this->prepareResponse($result);
    }

    /**
     * @Route("/{type}/{id}", requirements={"id": "\d+"}, name="update", methods={"PATCH"})
     *
     * @param Request $request
     * @param ProductCommonService $service
     * @return JsonResponse
     * @throws Exception
     */
    public function update(Request $request, ProductCommonService $service) : JsonResponse
    {
        $productModel = $service->updateEntityFromRequest($request);

        return $this->prepareResponse([
            'id' => $productModel->getProduct()->getId(),
            'message' => 'Продукт обновлён',
        ]);
    }

    /**
     * @Route("/{id}", requirements={"id": "\d+"}, name="delete", methods={"DELETE"})
     *
     * @param Request $request
     * @param ProductCommonService $service
     * @return JsonResponse
     */
    public function delete(Request $request, ProductCommonService $service) : JsonResponse
    {
        $id = $request->get('id');
        if ($service->deleteProduct($id)) {
            return $this->prepareResponse([
                'id' => $id,
                'message' => 'Продукт удалён',
            ]);
        }

        return $this->prepareErrorResponse('An error occurred while deleting product with id = ' . $id);
    }
}
