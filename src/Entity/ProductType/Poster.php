<?php

namespace App\Entity\ProductType;

use App\EntitySupport\Common\BaseEntity;
use App\EntitySupport\Common\BaseEntityTrait;
use App\EntitySupport\Behavior\ChangeTimeSavingBehavior;
use App\Entity\Product;
use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ProductType\PosterRepository")
 * @ORM\Table(name="product_type.poster")
 * @ORM\HasLifecycleCallbacks
 *
 * @method integer getId()
 * @method integer getCount()
 * @method string getFormatSize()
 * @method DateTime getCreatedAt()
 * @method DateTime getUpdatedAt()
 * @method Product getProduct()
 *
 * @method $this setCount(integer $count)
 * @method $this setFormatSize(string $formatSize)
 * @method $this setCreatedAt(DateTime $createdAt)
 * @method $this setUpdatedAt(DateTime $updatedAt)
 * @method $this setProduct(Product $product)
 */
class Poster extends BaseEntity
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

    /** @ORM\Column(type="string", length=255, nullable=true) */
    private $formatSize;

    /** @ORM\OneToOne(targetEntity="App\Entity\Product") */
    private $product;
}
