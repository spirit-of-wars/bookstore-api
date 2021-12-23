<?php

namespace App\Entity\ProductData;

use App\EntitySupport\Common\BaseEntity;
use App\EntitySupport\Common\BaseEntityTrait;
use App\EntitySupport\Behavior\ChangeTimeSavingBehavior;
use App\Entity\Product;
use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ProductData\Data1CRepository")
 * @ORM\Table(name="product_data.data1_c")
 * @ORM\HasLifecycleCallbacks
 *
 * @method integer getId()
 * @method string getIsbn()
 * @method string getId1c()
 * @method string getGroup1c()
 * @method string getItf14()
 * @method string getVat()
 * @method integer getQuantityPerPack()
 * @method integer getRrPrice()
 * @method integer getWeight()
 * @method integer getHeight()
 * @method integer getWidth()
 * @method integer getLength()
 * @method string getAgeLimit()
 * @method boolean getIsActive()
 * @method boolean getIsDefined()
 * @method DateTime getCreatedAt()
 * @method DateTime getUpdatedAt()
 * @method Product getProduct()
 *
 * @method $this setIsbn(string $isbn)
 * @method $this setId1c(string $id1c)
 * @method $this setGroup1c(string $group1c)
 * @method $this setItf14(string $itf14)
 * @method $this setVat(string $vat)
 * @method $this setQuantityPerPack(integer $quantityPerPack)
 * @method $this setRrPrice(integer $rrPrice)
 * @method $this setWeight(integer $weight)
 * @method $this setHeight(integer $height)
 * @method $this setWidth(integer $width)
 * @method $this setLength(integer $length)
 * @method $this setAgeLimit(string $ageLimit)
 * @method $this setIsActive(boolean $isActive)
 * @method $this setIsDefined(boolean $isDefined)
 * @method $this setCreatedAt(DateTime $createdAt)
 * @method $this setUpdatedAt(DateTime $updatedAt)
 * @method $this setProduct(Product $product)
 */
class Data1C extends BaseEntity
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
    private $isbn;

    /** @ORM\Column(type="string", length=255, nullable=true) */
    private $id1c;

    /** @ORM\Column(type="string", length=255, nullable=true) */
    private $group1c;

    /** @ORM\Column(type="string", length=255, nullable=true) */
    private $itf14;

    /** @ORM\Column(type="string", length=255, nullable=true) */
    private $vat;

    /** @ORM\Column(type="integer", nullable=true) */
    private $quantityPerPack;

    /** @ORM\Column(type="integer", nullable=true) */
    private $rrPrice;

    /** @ORM\Column(type="integer", nullable=true) */
    private $weight;

    /** @ORM\Column(type="integer", nullable=true) */
    private $height;

    /** @ORM\Column(type="integer", nullable=true) */
    private $width;

    /** @ORM\Column(type="integer", nullable=true) */
    private $length;

    /** @ORM\Column(type="string", length=255, nullable=true) */
    private $ageLimit;

    /** @ORM\Column(type="boolean", nullable=true) */
    private $isActive;

    /** @ORM\Column(type="boolean", nullable=true) */
    private $isDefined;

    /** @ORM\ManyToOne(targetEntity="App\Entity\Product", inversedBy="data1C") */
    private $product;
}
