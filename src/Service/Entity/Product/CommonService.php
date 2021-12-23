<?php

namespace App\Service\Entity\Product;

use App\Model\ProductModelProvider;
use App\Entity\Product;
use App\Model\ProductModel;
use App\Repository\ProductRepository;
use App\Request;
use App\Service\Entity\CategoryService;
use App\Service\Entity\CreativePartnerService;
use App\Service\Entity\EntityService;
use App\Service\Entity\PromoTagService;
use App\Service\Entity\SeriesService;
use App\Service\Entity\TagService;
use App\Service\Serializer\EntitySerializer;
use Exception;

/**
 * Class CommonService
 * @package App\Service\Entity
 *
 * @method ProductRepository getRepository()
 *
 * @property-read EntitySerializer EntitySerializer
 * @property-read RelationUpdateService RelationUpdateService
 * @property-read SeriesService SeriesService
 * @property-read CategoryService CategoryService
 * @property-read TagService TagService
 * @property-read PromoTagService PromoTagService
 * @property-read CreativePartnerService CreativePartnerService
 */
class CommonService extends EntityService
{
    /**
     * @return array|string[]
     */
    protected static function subscribedServicesMap()
    {
        return array_merge(parent::subscribedServicesMap(), [
            'EntitySerializer' => EntitySerializer::class,
            'RelationUpdateService' => RelationUpdateService::class,
            'CategoryService' => CategoryService::class,
            'SeriesService' => SeriesService::class,
            'TagService' => TagService::class,
            'PromoTagService' => PromoTagService::class,
            'CreativePartnerService' => CreativePartnerService::class,
        ]);
    }

    /**
     * @return string
     */
    protected function getEntityClassName()
    {
        return Product::class;
    }

    /**
     * @param Request $request
     * @param array $aliases
     * @return ProductModel
     * @throws Exception
     */
    public function createEntityFromRequest($request, $aliases = [])
    {
        $productModel = ProductModelProvider::buildFromProperties($request->all());
        return $productModel;
    }

    /**
     * @param Request $request
     * @param array $aliases
     * @return ProductModel
     */
    public function updateEntityFromRequest($request, $aliases = [])
    {
        $id = $request->get('id');
        $productModel = ProductModelProvider::getByProductId($id);
        $properties = $request->all();
        unset($properties['type']);
        $productModel->updateProperties($properties);

        return $productModel;
    }

    /**
     * @param int $id
     * @return ProductModel
     */
    public function getProductModel($id)
    {
        return ProductModelProvider::getByProductId($id);
    }

    /**
     * @param int $id
     * @return bool
     */
    public function deleteProduct($id)
    {
        $productModel = ProductModelProvider::getByProductId($id);
        $productModel->remove();

        return true;
    }

    /**
     * @param string $fullName
     * @param string $isbn
     * @return Product|null
     */
    public function getByFullNameAndIsbn(string $fullName, string $isbn) : ?Product
    {
        return $this->getRepository()->getByFullNameAndIsbn($fullName, $isbn);
    }

    /**
     * @param string $nameProduct
     * @return Product|null
     */
    public function getByFullName(string $nameProduct) : ?Product
    {
        return $this->getRepository()->getByFullName($nameProduct);
    }
}
