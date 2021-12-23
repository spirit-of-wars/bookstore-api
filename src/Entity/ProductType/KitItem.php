<?php

namespace App\Entity\ProductType;

use App\EntitySupport\Common\BaseEntity;
use App\EntitySupport\Common\BaseEntityTrait;
use App\EntitySupport\Behavior\ChangeTimeSavingBehavior;
use App\Entity\Product;
use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ProductType\KitItemRepository")
 * @ORM\Table(name="product_type.kit_item")
 * @ORM\HasLifecycleCallbacks
 *
 * @method integer getId()
 * @method integer getCount()
 * @method string getName()
 * @method DateTime getCreatedAt()
 * @method DateTime getUpdatedAt()
 * @method Product getProduct()
 * @method Kit getKit()
 *
 * @method $this setCount(integer $count)
 * @method $this setName(string $name)
 * @method $this setCreatedAt(DateTime $createdAt)
 * @method $this setUpdatedAt(DateTime $updatedAt)
 * @method $this setProduct(Product $product)
 * @method $this setKit(Kit $kit)
 */
class KitItem extends BaseEntity
{
    use BaseEntityTrait;
    use ChangeTimeSavingBehavior;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @ORM\Column(type="integer")
     * #App\Field(inForm="inputHidden")
     */
    private $id;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * #App\Constraint:GreaterThanOrEqual(1)
     */
    private $count;

    /** @ORM\Column(type="string", length=2048, nullable=true) */
    private $name;

    /** @ORM\ManyToOne(targetEntity="App\Entity\Product") */
    private $product;

    /** @ORM\ManyToOne(targetEntity="App\Entity\ProductType\Kit", inversedBy="items") */
    private $kit;
}
