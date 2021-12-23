<?php

namespace App\Model;

use App\Entity\Product;
use App\Entity\ProductData\Data1C;
use App\Entity\ProductEssence\Book;
use App\Entity\ProductEssence\Essence;
use App\Entity\Resource;
use App\EntitySupport\Behavior\ResourceGetterBehavior;
use App\EntitySupport\Common\Attribute\EntityAttribute;
use App\EntitySupport\Common\BaseEntity;
use App\EntitySupport\Common\EntityConstants;
use App\EntitySupport\Common\EntityRelation;
use App\Enum\ResourceAssigmentEnum;
use App\Exception\BadRequestException;
use App\Exception\MethodCallException;
use App\Mif;
use App\Service\Serializer\EntitySerializer;
use Doctrine\ORM\PersistentCollection;
use DateTime;
use Exception;


/**
 * Class ProductModel
 * @package App\Model
 *
 * @method int getProductId()
 */
class ProductModel
{
    /** @var Product|null */
    private ?Product $product;

    /** @var BaseEntity|null */
    private ?BaseEntity $productType;

    /** @var Essence|null */
    private ?Essence $essence;

    /** @var BaseEntity|null */
    private ?BaseEntity $essenceDetail;

    /** @var Data1C|null */
    private ?Data1C $data1C;

    /**
     * ProductModel constructor.
     * @param Product $productEntity
     * @param BaseEntity $productType
     * @param BaseEntity|null $essenceDetail
     */
    public function __construct(Product $productEntity, BaseEntity $productType, $essenceDetail = null)
    {
        $this->product = $productEntity;
        $this->essence = $productEntity->getEssence();
        $this->productType = $productType;
        $this->essenceDetail = $essenceDetail;

        $this->data1C = null;
    }

    /**
     * @param string $name
     * @param array $arguments
     * @return mixed
     * @throws MethodCallException
     */
    public function __call($name, $arguments)
    {
        if ($name == 'getProductId') {
            return $this->getProduct()->getId();
        }

        if ($this->product::getSchema()->hasMethod($name)) {
            return call_user_func_array([$this->product, $name], $arguments);
        }

        if ($this->essence::getSchema()->hasMethod($name)) {
            return call_user_func_array([$this->essence, $name], $arguments);
        }

        if ($this->productType::getSchema()->hasMethod($name)) {
            return call_user_func_array([$this->productType, $name], $arguments);
        }

        if ($this->essenceDetail) {
            if ($this->essenceDetail::getSchema()->hasMethod($name)) {
                return call_user_func_array([$this->essenceDetail, $name], $arguments);
            }
        }

        $className = self::class;
        throw new MethodCallException("Method doesn't exist {$className}::{$name}");
    }

    /**
     * @return Product
     */
    public function getProduct() : Product
    {
        return $this->product;
    }

    /**
     * @return BaseEntity
     */
    public function getProductType() : BaseEntity
    {
        return $this->productType;
    }

    /**
     * @return Essence
     */
    public function getEssence() : Essence
    {
        return $this->essence;
    }

    /**
     * @return BaseEntity
     */
    public function getEssenceDetail() : BaseEntity
    {
        return $this->essenceDetail;
    }

    /**
     * @return Data1C|null
     */
    public function getData1C()
    {
        if ($this->data1C === null) {
            $this->data1C = Mif::getServiceProvider()->Data1CService->getData1CByProduct($this->product);
        }

        return $this->data1C;
    }

    /**
     * @return void
     */
    public function save() : void
    {
        Mif::getPersistHolder()->hold();
        $this->essence->save();
        $this->product->save();
        $this->productType->save();
        if ($this->essenceDetail) {
            $this->essenceDetail->save();
        }
        Mif::getPersistHolder()->commit();
    }

    /**
     * @return void
     * @throws BadRequestException
     */
    public function remove() : void
    {
        Mif::getPersistHolder()->hold();

        $productService = $this->product::getService();
        $productService->removeEntity($this->product);

        $productTypeService = $this->productType::getService();
        $productTypeService->removeEntity($this->productType);

        $essenceService = $this->essence::getService();
        $essenceService->removeEntity($this->essence);

        if ($this->essenceDetail) {
            $essenceDetailService = $this->essenceDetail::getService();
            $essenceDetailService->removeEntity($this->essenceDetail);
        }

        Mif::getPersistHolder()->commit();
    }

    /**
     * @param array $properties
     * @return $this
     * @throws BadRequestException
     */
    public function updateProperties(array $properties)
    {
        Mif::getPersistHolder()->hold();

        $resourceService = Mif::getServiceProvider()->ResourceService;

        $resourceService->defineResourcesByAssignments(
            $this->product,
            [
                ResourceAssigmentEnum::IMAGE => 'resources',
            ],
            $properties
        );
        $productService = $this->product::getService();
        $productService->updateEntity($this->product, $properties);
        unset($properties['resources']);

        $resourceService->defineResourcesByAssignments(
            $this->essence,
            [
                ResourceAssigmentEnum::COVER_IMAGE => 'resources',
                ResourceAssigmentEnum::SPINE_IMAGE => 'resources',
            ],
            $properties
        );
        $essenceService = $this->essence::getService();
        $essenceService->updateEntity($this->essence, $properties);
        unset($properties['resources']);

        if ($this->essenceDetail) {
            $essenceDetailService = $this->essenceDetail::getService();
            $essenceDetailService->updateEntity($this->essenceDetail, $properties);
        }

        Mif::getPersistHolder()->commit();

        return $this;
    }

    /**
     * @return array
     */
    public function getAttributes()
    {
        $attributes = array_merge(
            $this->product->getAttributes(null, ['createdAt', 'updatedAt']),
            $this->productType->getAttributes(null, ['createdAt', 'updatedAt']),
            $this->essence->getAttributes(null, ['createdAt', 'updatedAt'])
        );

        if ($this->essenceDetail) {
            $attributes = array_merge(
                $attributes,
                $this->essenceDetail->getAttributes(null, ['createdAt', 'updatedAt'])
            );
        }

        $attributes['id'] = $this->product->getId();
        return $attributes;
    }

    /**
     * @return array
     */
    public function toArray() //todo возможно тут этому не место
    {
        $attributes = []; //TODO    //$this->getAttributes();

        $serialize = new EntitySerializer();

        foreach ($attributes as &$attr) {
            if ($attr instanceof PersistentCollection) {
                $collectionEntities = $attr->toArray();
                $res = [];
                /** @var  BaseEntity $entity **/
                foreach ($collectionEntities as $entity) {
                    $res[] = $serialize->serialize($entity);
                }
                $attr = $res;
            } elseif ($attr instanceof BaseEntity) {
                $attr = $serialize->serialize($attr);
            } elseif ($attr instanceof DateTime) {
                $attr = $attr->format('Y-m-d\TH:i:sP');
            }
        }
        unset($attr);

        return $attributes;
    }
}
