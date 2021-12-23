<?php

namespace App\Entity;

use App\EntitySupport\Common\BaseEntity;
use App\EntitySupport\Common\BaseEntityTrait;
use App\EntitySupport\Behavior\ChangeTimeSavingBehavior;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use App\Entity\ProductEssence\Essence as ProductEssenceEssence;
use App\Entity\VirtualPageResource\Banner as VirtualPageResourceBanner;
use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ResourceRepository")
 * @ORM\Table(name="common.resource")
 * @ORM\HasLifecycleCallbacks
 *
 * @method integer getId()
 * @method string getType()
 * @method string getName()
 * @method string getAssigment()
 * @method string getPath()
 * @method string getFormat()
 * @method array getResProperties()
 * @method mixed getFromResProperties(string $key)
 * @method string getDescription()
 * @method DateTime getCreatedAt()
 * @method DateTime getUpdatedAt()
 * @method Collection getProducts()
 * @method Collection getEssences()
 * @method Collection getBanners()
 *
 * @method $this setType(string $type)
 * @method $this setName(string $name)
 * @method $this setAssigment(string $assigment)
 * @method $this setPath(string $path)
 * @method $this setFormat(string $format)
 * @method $this setResProperties(array $resProperties)
 * @method $this setDescription(string $description)
 * @method $this setCreatedAt(DateTime $createdAt)
 * @method $this setUpdatedAt(DateTime $updatedAt)
 *
 * @method $this addToResProperties(string|array $keyOrMap, mixed $value = null)
 * @method $this removeFromResProperties(string $key)
 * @method $this addToProducts(Product $product)
 * @method $this removeFromProducts(Product $product)
 * @method $this addToEssences(ProductEssenceEssence $productEssenceEssence)
 * @method $this removeFromEssences(ProductEssenceEssence $productEssenceEssence)
 * @method $this addToBanners(VirtualPageResourceBanner $virtualPageResourceBanner)
 * @method $this removeFromBanners(VirtualPageResourceBanner $virtualPageResourceBanner)
 */
class Resource extends BaseEntity
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
     * @ORM\Column(type="string", length=255, nullable=false)
     * #App\Constraint:Enum("ResourceTypeEnum")
     */
    private $type;

    /** @ORM\Column(type="string", length=255, nullable=false) */
    private $name;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * #App\Constraint:Enum("ResourceAssigmentEnum")
     */
    private $assigment;

    /** @ORM\Column(type="string", length=255, nullable=true) */
    private $path;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * #App\Constraint:Enum("FileTypeEnum")
     */
    private $format;

    /** @ORM\Column(type="dict", nullable=true) */
    private $resProperties;

    /** @ORM\Column(type="text", nullable=true) */
    private $description;

    /** @ORM\ManyToMany(targetEntity="App\Entity\Product", mappedBy="resources") */
    private $products;

    /** @ORM\ManyToMany(targetEntity="App\Entity\ProductEssence\Essence", mappedBy="resources") */
    private $essences;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\VirtualPageResource\Banner", inversedBy="resources")
     * @ORM\JoinTable(name="util_rel.resource__virtual_page_resource__banner")
     */
    private $banners;

    public function __construct()
    {
        $this->products = new ArrayCollection();
        $this->essences = new ArrayCollection();
        $this->banners = new ArrayCollection();
    }
}
