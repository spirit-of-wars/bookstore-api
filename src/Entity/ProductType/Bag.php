<?php

namespace App\Entity\ProductType;

use App\EntitySupport\Common\BaseEntity;
use App\EntitySupport\Common\BaseEntityTrait;
use App\EntitySupport\Behavior\ChangeTimeSavingBehavior;
use App\Entity\Product;
use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ProductType\BagRepository")
 * @ORM\Table(name="product_type.bag")
 * @ORM\HasLifecycleCallbacks
 *
 * @method integer getId()
 * @method string getBox()
 * @method string getMaterial()
 * @method integer getWidth()
 * @method integer getHeight()
 * @method integer getDepth()
 * @method integer getHandleLength()
 * @method integer getHandleWidth()
 * @method DateTime getCreatedAt()
 * @method DateTime getUpdatedAt()
 * @method Product getProduct()
 *
 * @method $this setBox(string $box)
 * @method $this setMaterial(string $material)
 * @method $this setWidth(integer $width)
 * @method $this setHeight(integer $height)
 * @method $this setDepth(integer $depth)
 * @method $this setHandleLength(integer $handleLength)
 * @method $this setHandleWidth(integer $handleWidth)
 * @method $this setCreatedAt(DateTime $createdAt)
 * @method $this setUpdatedAt(DateTime $updatedAt)
 * @method $this setProduct(Product $product)
 */
class Bag extends BaseEntity
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
    private $box;

    /** @ORM\Column(type="string", length=255, nullable=true) */
    private $material;

    /** @ORM\Column(type="integer", nullable=true) */
    private $width;

    /** @ORM\Column(type="integer", nullable=true) */
    private $height;

    /** @ORM\Column(type="integer", nullable=true) */
    private $depth;

    /** @ORM\Column(type="integer", nullable=true) */
    private $handleLength;

    /** @ORM\Column(type="integer", nullable=true) */
    private $handleWidth;

    /** @ORM\OneToOne(targetEntity="App\Entity\Product") */
    private $product;
}
