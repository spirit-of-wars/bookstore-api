<?php

namespace App\Model;

use App\Entity\Product;
use App\Entity\ProductEssence\Book;
use App\Entity\ProductEssence\Essence;
use App\EntitySupport\Common\BaseEntity;
use App\Enum\ProductTypeEnum;
use App\Mif;
use App\Repository\BaseRepository;
use App\Repository\ProductEssence\BookRepository;
use App\Repository\ProductRepository;
use App\Service\Entity\EssenceService;
use Exception;

/**
 * Class ProductModelProvider
 * @package App\Model
 */
class ProductModelProvider
{
    /**
     * @param $properties
     * @return ProductModel
     * @throws Exception
     */
    public static function buildFromProperties($properties)
    {
        Mif::getPersistHolder()->hold();

        $productService = Product::getService();
        /** @var Product $product */
        $product = $productService->createEntity($properties);

        $productTypeName = $properties['type'];
        /** @var BaseEntity $productTypeClass (static) */
        $productTypeClass = ProductTypeEnum::getEntityClassName($productTypeName);

        /** @var Essence $essence */
        list($essence, $essenceDetail) = self::findOrCreateEssence($productTypeClass, $properties);
        $product->setEssence($essence);

        $productTypeService = $productTypeClass::getService();
        $productType = $productTypeService->createEntity($properties);

        $productType->setProduct($product);

        Mif::getPersistHolder()->commit();

        $productModel = new ProductModel($product, $productType, $essenceDetail);
        return $productModel;
    }

    /**
     * @param int $productId
     * @return ProductModel
     */
    public static function getByProductId($productId)
    {
        $product = self::getProductEntity($productId);
        $productDetail = self::getProductDetailEntity($product);
        $bookEssence = self::getBookEssenceEntity($product->getEssence());

        return new ProductModel($product, $productDetail, $bookEssence);
    }

    /**
     * @param $productId
     * @return Product|null
     */
    private static function getProductEntity($productId)
    {
        /** @var ProductRepository $productRepository */
        $productRepository = Mif::getDoctrine()->getRepository(Product::class);

        return $productRepository->find($productId) ?? null;
    }

    /**
     * @param Product $product
     * @return BaseEntity|null
     */
    private static function getProductDetailEntity(Product $product) : ?object
    {
        $entityClass = ProductTypeEnum::getEntityClassName($product->getType());
        /** @var BaseRepository $repo */
        $repo = Mif::getDoctrine()->getRepository($entityClass);

        return $repo->findOneBy(['product' => $product]) ?? null;
    }

    /**
     * @param Essence $essence
     * @return Essence|null
     */
    private static function getBookEssenceEntity(Essence $essence) : ?Book
    {
        /** @var BookRepository $repo */
        $repo = Mif::getDoctrine()->getRepository(Book::class);

        return $repo->findOneBy(['essence' => $essence]) ?? null;
    }

    /**
     * @return array [Essence, BaseEntity]
     */
    private static function findOrCreateEssence($productTypeClass, $properties)
    {
        /** @var EssenceService $essenceService */
        $essenceService = Essence::getService();

        $essence = null;
        $essenceDetail = null;
        $id = $properties['essenceId'] ?? null;
        if ($id) {
            $essence = $essenceService->getEntity($id);
        }

        if ($essence === null) {
            $essence = $essenceService->createEntity($properties);

            $essenceDetail = null;
            /** @var BaseEntity $essenceDetailClass (static) */
            $essenceDetailClass = ProductModelMapper::defineEssenceDependency($productTypeClass);
            if ($essenceDetailClass) {
                $essenceDetailService = $essenceDetailClass::getService();
                $essenceDetail = $essenceDetailService->createEntity($properties);
                $essenceDetail->setEssence($essence);
            }
        }

        return [$essence, $essenceDetail];
    }
}
