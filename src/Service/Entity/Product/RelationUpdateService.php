<?php

namespace App\Service\Entity\Product;

use App\Entity\CreativePartner;
use App\Entity\Product;
use App\Entity\ProductGroup\PromoTag;
use App\Entity\ProductGroup\Tag;
use App\Request;
use App\Service\Entity\CreativePartnerService;
use App\Service\Entity\EntityService;
use App\Service\Entity\PromoTagService;
use App\Service\Entity\SeriesService;
use App\Service\Entity\TagService;
use App\Service\Entity\CategoryService;
use Exception;

/**
 * Class RelationUpdateService
 * @package App\Service\Entity\Product
 *
 * @property-read SeriesService SeriesService
 * @property-read PromoTagService PromoTagService
 * @property-read TagService TagService
 * @property-read CreativePartnerService CreativePartnerService
 * @property-read CategoryService CategoryService
 */
class RelationUpdateService extends EntityService
{
    //todo проверить что нигде не юзается и удалить
    /**
     * @return array|string[]
     */
    protected static function subscribedServicesMap()
    {
        return array_merge(parent::subscribedServicesMap(), [
            'SeriesService' => SeriesService::class,
            'PromoTagService' => PromoTagService::class,
            'TagService' => TagService::class,
            'CreativePartnerService' => CreativePartnerService::class,
            'CategoryService' => CategoryService::class,
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
     * @param Product $product
     * @param Request $request
     */
    public function update($product, $request)
    {
        if ($request->has('creativePartners')) {
            $this->updateCreativePartners($product, $request->get('creativePartners') ?? []);
        }

        if ($request->has('series')) {
            $this->updateSeries($product, $request->get('series'));
        }

        if ($request->has('tags')) {
            $this->updateTags($product, $request->get('tags') ?? []);
        }

        if ($request->has('promoTags')) {
            $this->updatePromoTags($product, $request->get('promoTags') ?? []);
        }

        if ($request->has('categories')) {
            $this->updateCategories($product, $request->get('categories') ?? []);
        }
    }


    //TODO-product вызовы add..., remove... должны быть опосредованы через ProductModel


    /**
     * @param Product $product
     * @param array $newTagIds
     * @return void
     * @throws Exception
     */
    private function updateTags(Product $product, array $newTagIds = []) : void
    {
        $currentProductTagsCollection = $product->getTags();
        $currentProductTags = [];

        /** @var Tag $tag */
        foreach ($currentProductTagsCollection as $tag) {
            $currentProductTags[$tag->getId()] = $tag;
        }

        $idsForAdd = array_diff(
            $newTagIds,
            array_keys($currentProductTags)
        );
        $idsForDelete = array_diff(
            array_keys($currentProductTags),
            $newTagIds
        );

        foreach ($idsForDelete as $id) {
            $product->removeFromTags($currentProductTags[$id]);
        }

        foreach ($idsForAdd as $id) {
            /** @var Tag $tag */
            $tag = $this->TagService->getEntity($id);
            if (!$tag) {
                throw new Exception('Tag is not exist. Id: ' . $id);
            }
            $product->addToTags($tag);
        }

        $product->save();
    }

    /**
     * @param Product $product
     * @param array $newPromoTagIds
     * @return void
     * @throws Exception
     */
    private function updatePromoTags(Product $product, array $newPromoTagIds = []) : void
    {
        $currentPromoTagCollection = $product->getPromoTags();
        $currentPromoTags = [];

        /** @var PromoTag $promoTag */
        foreach ($currentPromoTagCollection as $promoTag) {
            $currentPromoTags[$promoTag->getId()] = $promoTag;
        }


        $idsForAdd = array_diff(
            $newPromoTagIds,
            array_keys($currentPromoTags)
        );
        $idsForDelete = array_diff(
            array_keys($currentPromoTags),
            $newPromoTagIds
        );

        foreach ($idsForDelete as $id) {
            $product->removeFromPromoTags($currentPromoTags[$id]);
        }

        foreach ($idsForAdd as $id) {
            /** @var Tag $tag */
            $promoTag = $this->PromoTagService->getEntity($id);
            if (!$promoTag) {
                throw new Exception('Promo tag is not exist. Id: ' . $id);
            }
            $product->addToPromoTags($promoTag);
        }

        $product->save();

    }

    /**
     * @param Product $product
     * @param int|null $idSeries
     */
    private function updateSeries(Product $product, ?int $idSeries)
    {
        $series = null;
        if (!is_null($idSeries)) {
            $series = $this->SeriesService->getEntity($idSeries);

            if (is_null($series)) {
                return;
            }
        }

        //TODO структура поменялась
        $product->setSeries($series);
    }

    /**
     * @param Product $product
     * @param array $newCreativePartnerIds
     * @throws Exception
     */
    private function updateCreativePartners(Product $product, array $newCreativePartnerIds = [])
    {
        $creativePartnerService = $this->CreativePartnerService;

        /** @var CreativePartner[] $currentCreativePartners */
        $currentCreativePartnersCollection = $product->getCreativePartners();

        $currentCreativePartners = [];
        foreach ($currentCreativePartnersCollection as $creativePartner) {
            $currentCreativePartners[$creativePartner->getId()] = $creativePartner;
        }

        $idsForAdd = array_diff(
            $newCreativePartnerIds,
            array_keys($currentCreativePartners)
        );
        $idsForDelete = array_diff(
            array_keys($currentCreativePartners),
            $newCreativePartnerIds
        );

        foreach ($idsForDelete as $id) {
            $product->removeFromCreativePartners($currentCreativePartners[$id]);
        }

        foreach ($idsForAdd as $id) {
            /** @var CreativePartner $creativePartner */
            $creativePartner = $creativePartnerService->getEntity($id);
            if (!$creativePartner) {
                throw new Exception('Creative partner is not exist. Id: ' . $id);
            }
            $product->addToCreativePartners($creativePartner);
        }
    }

    /**
     * @param Product $product
     * @param array $categoryIds
     * @throws \Exception
     * @return void
     */
    private function updateCategories(Product $product, array $categoryIds = []) : void
    {
        $categoryService = $this->CategoryService;

        /** @var Category[] $currentCategoriesCollection */
        $currentCategoriesCollection = $product->getCategories();
        $currentCategories = [];

        foreach ($currentCategoriesCollection as $category) {
            $currentCategories[$category->getId()] = $category;
        }

        $idsForAdd = array_diff(
            $categoryIds,
            array_keys($currentCategories)
        );
        $idsForDelete = array_diff(
            array_keys($currentCategories),
            $categoryIds,
        );

        foreach ($idsForDelete as $id) {
            $product->removeFromCategories($currentCategories[$id]);
        }

        foreach ($idsForAdd as $id) {
            /** @var Category $category */
            $category = $categoryService->getEntity($id);
            if (!$category) {
                throw new \Exception('Category is not exist. Id: ' . $id);
            }
            $product->addToCategories($category);
        }
    }
}
