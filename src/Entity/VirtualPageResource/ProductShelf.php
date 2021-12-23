<?php

namespace App\Entity\VirtualPageResource;

use App\EntitySupport\Common\BaseEntity;
use App\EntitySupport\Common\BaseEntityTrait;
use App\EntitySupport\Behavior\ChangeTimeSavingBehavior;
use App\EntitySupport\Behavior\ShelfLoadBehavior;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use App\Entity\Product;
use App\Entity\VirtualPage;
use App\Entity\ProductGroup\Category as ProductGroupCategory;
use App\Entity\ProductGroup\Tag as ProductGroupTag;
use App\Entity\ProductGroup\PromoTag as ProductGroupPromoTag;
use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\VirtualPageResource\ProductShelfRepository")
 * @ORM\Table(name="virtual_page_resource.product_shelf")
 * @ORM\HasLifecycleCallbacks
 *
 * @method integer getId()
 * @method string getType()
 * @method string getName()
 * @method string getCode()
 * @method string getDescription()
 * @method DateTime getCreatedAt()
 * @method DateTime getUpdatedAt()
 * @method Collection getProducts()
 * @method Collection getVirtualPages()
 * @method ProductGroupCategory getCategory()
 * @method ProductGroupTag getTag()
 * @method ProductGroupPromoTag getPromoTag()
 *
 * @method $this setType(string $type)
 * @method $this setName(string $name)
 * @method $this setCode(string $code)
 * @method $this setDescription(string $description)
 * @method $this setCreatedAt(DateTime $createdAt)
 * @method $this setUpdatedAt(DateTime $updatedAt)
 * @method $this setCategory(ProductGroupCategory $category)
 * @method $this setTag(ProductGroupTag $tag)
 * @method $this setPromoTag(ProductGroupPromoTag $promoTag)
 *
 * @method $this addToProducts(Product $product)
 * @method $this removeFromProducts(Product $product)
 * @method $this addToVirtualPages(VirtualPage $virtualPage)
 * @method $this removeFromVirtualPages(VirtualPage $virtualPage)
 */
class ProductShelf extends BaseEntity
{
    use BaseEntityTrait;
    use ChangeTimeSavingBehavior;
    use ShelfLoadBehavior;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @ORM\Column(type="integer")
     * #App\Field(inForm="inputHidden")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=false)
     * #App\Constraint:Enum("ShelfTypeEnum")
     */
    private $type;

    /** @ORM\Column(type="string", length=255, nullable=false) */
    private $name;

    /** @ORM\Column(type="string", length=255, nullable=false) */
    private $code;

    /** @ORM\Column(type="text", nullable=true) */
    private $description;

    /** @ORM\ManyToMany(targetEntity="App\Entity\Product", mappedBy="productShelves") */
    private $products;

    /** @ORM\ManyToMany(targetEntity="App\Entity\VirtualPage", mappedBy="productShelves") */
    private $virtualPages;

    /** @ORM\ManyToOne(targetEntity="App\Entity\ProductGroup\Category") */
    private $category;

    /** @ORM\ManyToOne(targetEntity="App\Entity\ProductGroup\Tag") */
    private $tag;

    /** @ORM\ManyToOne(targetEntity="App\Entity\ProductGroup\PromoTag") */
    private $promoTag;

    public function __construct()
    {
        $this->products = new ArrayCollection();
        $this->virtualPages = new ArrayCollection();
    }
}
