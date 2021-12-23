<?php

namespace App\Entity;

use App\EntitySupport\Common\BaseEntity;
use App\EntitySupport\Common\BaseEntityTrait;
use App\EntitySupport\Behavior\ProductBehavior;
use App\EntitySupport\Behavior\ChangeTimeSavingBehavior;
use App\EntitySupport\Behavior\ResourceGetterBehavior;
use DateTime;
use App\Entity\ProductEssence\Essence as ProductEssenceEssence;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use App\Entity\ProductGroup\PromoTag as ProductGroupPromoTag;
use App\Entity\ProductData\PromoData as ProductDataPromoData;
use App\Entity\ProductData\Data1C as ProductDataData1C;
use App\Entity\VirtualPageResource\ProductShelf as VirtualPageResourceProductShelf;
use App\Entity\VirtualPageResource\Factoid as VirtualPageResourceFactoid;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ProductRepository")
 * @ORM\Table(name="common.product")
 * @ORM\HasLifecycleCallbacks
 *
 * @method integer getId()
 * @method integer getOldModxId()
 * @method string getIdMif()
 * @method string getType()
 * @method string getSlug()
 * @method string getUri()
 * @method integer getPrice()
 * @method string getLifeCycleStatus()
 * @method string getReleaseData()
 * @method DateTime getStartSaleDate()
 * @method DateTime getPlanedStartSaleDate()
 * @method boolean getIsDimensionlessForPresent()
 * @method boolean getIsAvailableForPresent()
 * @method DateTime getCreatedAt()
 * @method DateTime getUpdatedAt()
 * @method ProductEssenceEssence getEssence()
 * @method Collection getPromoTags()
 * @method ProductDataPromoData getPromoData()
 * @method Collection getData1C()
 * @method Collection getProductShelves()
 * @method VirtualPageResourceFactoid getFactoid()
 * @method Collection getResources()
 *
 * @method $this setOldModxId(integer $oldModxId)
 * @method $this setIdMif(string $idMif)
 * @method $this setType(string $type)
 * @method $this setSlug(string $slug)
 * @method $this setUri(string $uri)
 * @method $this setPrice(integer $price)
 * @method $this setLifeCycleStatus(string $lifeCycleStatus)
 * @method $this setReleaseData(string $releaseData)
 * @method $this setStartSaleDate(DateTime $startSaleDate)
 * @method $this setPlanedStartSaleDate(DateTime $planedStartSaleDate)
 * @method $this setIsDimensionlessForPresent(boolean $isDimensionlessForPresent)
 * @method $this setIsAvailableForPresent(boolean $isAvailableForPresent)
 * @method $this setCreatedAt(DateTime $createdAt)
 * @method $this setUpdatedAt(DateTime $updatedAt)
 * @method $this setEssence(ProductEssenceEssence $essence)
 * @method $this setPromoData(ProductDataPromoData $promoData)
 * @method $this setFactoid(VirtualPageResourceFactoid $factoid)
 *
 * @method $this addToPromoTags(ProductGroupPromoTag $productGroupPromoTag)
 * @method $this removeFromPromoTags(ProductGroupPromoTag $productGroupPromoTag)
 * @method $this addToData1C(ProductDataData1C $productDataData1C)
 * @method $this removeFromData1C(ProductDataData1C $productDataData1C)
 * @method $this addToProductShelves(VirtualPageResourceProductShelf $virtualPageResourceProductShelf)
 * @method $this removeFromProductShelves(VirtualPageResourceProductShelf $virtualPageResourceProductShelf)
 * @method $this addToResources(Resource $resource)
 * @method $this removeFromResources(Resource $resource)
 */
class Product extends BaseEntity
{
    use BaseEntityTrait;
    use ProductBehavior;
    use ChangeTimeSavingBehavior;
    use ResourceGetterBehavior;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @ORM\Column(type="integer")
     * #App\Field(inForm="inputHidden")
     */
    private $id;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * #App\Field(inForm="hidden")
     */
    private $oldModxId;

    /** @ORM\Column(type="string", length=255, nullable=true) */
    private $idMif;

    /**
     * @ORM\Column(type="string", length=255, nullable=false)
     * #App\Constraint:Enum("ProductTypeEnum")
     */
    private $type;

    /** @ORM\Column(type="string", length=255, nullable=true) */
    private $slug;

    /** @ORM\Column(type="string", length=255, nullable=true) */
    private $uri;

    /** @ORM\Column(type="integer", nullable=true) */
    private $price;

    /**
     * @ORM\Column(type="string", length=10, nullable=false)
     * #App\Constraint:Enum("ProductLifeCycleStatusEnum")
     */
    private $lifeCycleStatus = 'created';

    /** @ORM\Column(type="text", nullable=true) */
    private $releaseData;

    /** @ORM\Column(type="datetime", nullable=true) */
    private $startSaleDate;

    /** @ORM\Column(type="datetime", nullable=true) */
    private $planedStartSaleDate;

    /** @ORM\Column(type="boolean", nullable=false) */
    private $isDimensionlessForPresent;

    /** @ORM\Column(type="boolean", nullable=false) */
    private $isAvailableForPresent;

    /** @ORM\ManyToOne(targetEntity="App\Entity\ProductEssence\Essence", inversedBy="products") */
    private $essence;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\ProductGroup\PromoTag", inversedBy="products")
     * @ORM\JoinTable(name="util_rel.product__product_group__promo_tag")
     */
    private $promoTags;

    /** @ORM\OneToOne(targetEntity="App\Entity\ProductData\PromoData", mappedBy="product") */
    private $promoData;

    /** @ORM\OneToMany(targetEntity="App\Entity\ProductData\Data1C", mappedBy="product") */
    private $data1C;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\VirtualPageResource\ProductShelf", inversedBy="products")
     * @ORM\JoinTable(name="util_rel.product__virtual_page_resource__product_shelf")
     */
    private $productShelves;

    /** @ORM\OneToOne(targetEntity="App\Entity\VirtualPageResource\Factoid", mappedBy="product") */
    private $factoid;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Resource", inversedBy="products")
     * @ORM\JoinTable(name="util_rel.product__resource")
     */
    private $resources;

    public function __construct()
    {
        $this->promoTags = new ArrayCollection();
        $this->data1C = new ArrayCollection();
        $this->productShelves = new ArrayCollection();
        $this->resources = new ArrayCollection();
    }
}
