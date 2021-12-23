<?php

namespace App\Entity\ProductType;

use App\EntitySupport\Common\BaseEntity;
use App\EntitySupport\Common\BaseEntityTrait;
use App\EntitySupport\Behavior\ChangeTimeSavingBehavior;
use App\Entity\Product;
use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ProductType\BadgeRepository")
 * @ORM\Table(name="product_type.badge")
 * @ORM\HasLifecycleCallbacks
 *
 * @method integer getId()
 * @method string getMaterial()
 * @method integer getWidth()
 * @method integer getHeight()
 * @method DateTime getCreatedAt()
 * @method DateTime getUpdatedAt()
 * @method Product getProduct()
 *
 * @method $this setMaterial(string $material)
 * @method $this setWidth(integer $width)
 * @method $this setHeight(integer $height)
 * @method $this setCreatedAt(DateTime $createdAt)
 * @method $this setUpdatedAt(DateTime $updatedAt)
 * @method $this setProduct(Product $product)
 */
class Badge extends BaseEntity
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

    /** @ORM\Column(type="string", length=255, nullable=true) */
    private $material;

    /** @ORM\Column(type="integer", nullable=true) */
    private $width;

    /** @ORM\Column(type="integer", nullable=true) */
    private $height;

    /** @ORM\OneToOne(targetEntity="App\Entity\Product") */
    private $product;
}
