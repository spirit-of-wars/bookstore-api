<?php

namespace App\Service\Entity;

use App\Entity\VirtualPageResource\ProductShelf;
use App\Enum\ShelfTypeEnum;
use App\Exception\BadRequestException;
use App\Exception\EntityNotFoundException;
use App\Repository\VirtualPageResource\ProductShelfRepository;
use App\Request;
use App\Util\ShelfLoadRules\CommonShelfLoadRules;
use App\Util\ShelfLoadRules\ShelfLoadRuleHelper;
use Doctrine\ORM\NonUniqueResultException;

/**
 * Class ProductShelfService
 * @package App\Service\Entity
 *
 * @method ProductShelfRepository getRepository()
 * @method ProductShelf getEntity($id)
 * @method ProductShelf createEntity($properties)
 */
class ProductShelfService extends EntityService
{
    /**
     * @return string
     */
    protected function getEntityClassName()
    {
        return ProductShelf::class;
    }

    /**
     * @param Request $request
     * @param array $aliases
     * @return ProductShelf
     */
    public function createEntityFromRequest($request, $aliases = [])
    {
        $properties = $this->extractPropertiesFromRequest($request, $aliases);
        $this->validateProperties($properties['type'] ?? null, $properties);

        if ($this->getDuplicate($properties['name'], $properties['code'], $properties['type'])) {
            throw new BadRequestException('Такая полка уже существует');
        }

        return $this->createEntity($properties);
    }

    /**
     * @param Request $request
     * @param array $aliases
     * @return ProductShelf
     */
    public function updateEntityFromRequest($request, $aliases = [])
    {
        $entity = $this->getEntity($request->get('id'));
        if (!$entity) {
            throw new EntityNotFoundException('Объект не найден');
        }

        $properties = $this->extractPropertiesFromRequest($request, $aliases);
        $this->validateProperties($entity->getType(), $properties);

        $this->updateEntity($entity, $properties);
        return $entity;
    }

    /**
     * @param string $code
     * @param string|null $page
     * @param string|null $limit
     * @return array
     * @throws NonUniqueResultException
     */
    public function getProducts(string $code, ?string $page = null, ?string $limit = null) : array
    {
        //TODO надо всё проверить. Полкам в плане взаимоотношений с продуктами нужен рефакторинг

        /** @var ProductShelf $productShelf */
        $productShelf = $this->getRepository()->getByCode($code);

        if (is_null($productShelf)) {
            return [];
        }

        if ($productShelf->getType() == ShelfTypeEnum::SHELF_CUSTOM) {
            $shelf['shelf'] = $productShelf->getAttributes(null, ['createdAt', 'updatedAt']);
            $shelf['products'][] = ShelfLoadRuleHelper::getArrayProducts($productShelf->getProducts()->toArray());
            return $shelf;
        }

        /** @var CommonShelfLoadRules $shelfLoadRules */
        $shelfLoadRules = $productShelf->getShelfLoadRules();
        return $shelfLoadRules->getProductsForShelf($productShelf, $page, $limit);
    }

    /**
     * @param string $type
     * @param array $properties
     * @throws BadRequestException
     */
    private function validateProperties($type, $properties)
    {
        if ($type == ShelfTypeEnum::SHELF_AUTO) {
            if (array_key_exists('products', $properties)) {
                throw new BadRequestException('Полка будет собираться автоматически. Нельзя прикрепить продукты.');
            }
        }
    }

    /**
     * @param array $promoTagIds
     * @return array
     */
    public function getByPromoTagIds(array $promoTagIds) : array
    {
        return $this->getRepository()->findByPromoTagIds($promoTagIds);
    }

    /**
     * @param string $name
     * @param string $code
     * @param string $type
     * @return ProductShelf|null
     * @throws NonUniqueResultException
     */
    private function getDuplicate(string $name, string $code, string $type) : ?ProductShelf
    {
        return $this->getRepository()->getDuplicate($name, $code, $type);
    }
}
