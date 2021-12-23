<?php

namespace App\Entity;

use App\EntitySupport\Common\BaseEntity;
use App\EntitySupport\Common\BaseEntityTrait;
use App\EntitySupport\Behavior\ChangeTimeSavingBehavior;
use App\EntitySupport\Behavior\VirtualPageBehavior;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use App\Entity\ProductGroup\Category as ProductGroupCategory;
use App\Entity\VirtualPageResource\BannerShelf as VirtualPageResourceBannerShelf;
use App\Entity\VirtualPageResource\ProductShelf as VirtualPageResourceProductShelf;
use App\Entity\VirtualPageResource\Banner as VirtualPageResourceBanner;
use App\Entity\VirtualPageResource\Factoid as VirtualPageResourceFactoid;
use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\VirtualPageRepository")
 * @ORM\Table(name="common.virtual_page")
 * @ORM\HasLifecycleCallbacks
 *
 * @method integer getId()
 * @method string getTitle()
 * @method string getSlug()
 * @method string getDescription()
 * @method integer getPosition()
 * @method integer getLevel()
 * @method boolean getIsMenu()
 * @method DateTime getCreatedAt()
 * @method DateTime getUpdatedAt()
 * @method Collection getSubVirtualPages()
 * @method VirtualPage getParentVirtualPage()
 * @method ProductGroupCategory getCategory()
 * @method Resource getIcon()
 * @method Collection getBannerShelves()
 * @method Collection getProductShelves()
 * @method Collection getBanners()
 * @method Collection getFactoids()
 *
 * @method $this setTitle(string $title)
 * @method $this setSlug(string $slug)
 * @method $this setDescription(string $description)
 * @method $this setPosition(integer $position)
 * @method $this setLevel(integer $level)
 * @method $this setIsMenu(boolean $isMenu)
 * @method $this setCreatedAt(DateTime $createdAt)
 * @method $this setUpdatedAt(DateTime $updatedAt)
 * @method $this setParentVirtualPage(VirtualPage $parentVirtualPage)
 * @method $this setCategory(ProductGroupCategory $category)
 * @method $this setIcon(Resource $icon)
 *
 * @method $this addToSubVirtualPages(VirtualPage $virtualPage)
 * @method $this removeFromSubVirtualPages(VirtualPage $virtualPage)
 * @method $this addToBannerShelves(VirtualPageResourceBannerShelf $virtualPageResourceBannerShelf)
 * @method $this removeFromBannerShelves(VirtualPageResourceBannerShelf $virtualPageResourceBannerShelf)
 * @method $this addToProductShelves(VirtualPageResourceProductShelf $virtualPageResourceProductShelf)
 * @method $this removeFromProductShelves(VirtualPageResourceProductShelf $virtualPageResourceProductShelf)
 * @method $this addToBanners(VirtualPageResourceBanner $virtualPageResourceBanner)
 * @method $this removeFromBanners(VirtualPageResourceBanner $virtualPageResourceBanner)
 * @method $this addToFactoids(VirtualPageResourceFactoid $virtualPageResourceFactoid)
 * @method $this removeFromFactoids(VirtualPageResourceFactoid $virtualPageResourceFactoid)
 */
class VirtualPage extends BaseEntity
{
    use BaseEntityTrait;
    use ChangeTimeSavingBehavior;
    use VirtualPageBehavior;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @ORM\Column(type="integer")
     * #App\Field(inForm="inputHidden")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * #App\Constraint:MifName()
     */
    private $title;

    /** @ORM\Column(type="text", nullable=true) */
    private $slug;

    /** @ORM\Column(type="text", nullable=true) */
    private $description;

    /** @ORM\Column(type="integer", nullable=true) */
    private $position;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * #App\Field(inForm="inputHidden")
     */
    private $level;

    /** @ORM\Column(type="boolean", nullable=false) */
    private $isMenu;

    /** @ORM\OneToMany(targetEntity="App\Entity\VirtualPage", mappedBy="parentVirtualPage") */
    private $subVirtualPages;

    /** @ORM\ManyToOne(targetEntity="App\Entity\VirtualPage", inversedBy="subVirtualPages") */
    private $parentVirtualPage;

    /** @ORM\OneToOne(targetEntity="App\Entity\ProductGroup\Category") */
    private $category;

    /** @ORM\OneToOne(targetEntity="App\Entity\Resource") */
    private $icon;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\VirtualPageResource\BannerShelf", inversedBy="virtualPages")
     * @ORM\JoinTable(name="util_rel.virtual_page__virtual_page_resource__banner_shelf")
     */
    private $bannerShelves;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\VirtualPageResource\ProductShelf", inversedBy="virtualPages")
     * @ORM\JoinTable(name="util_rel.virtual_page__virtual_page_resource__product_shelf")
     */
    private $productShelves;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\VirtualPageResource\Banner", inversedBy="virtualPages")
     * @ORM\JoinTable(name="util_rel.virtual_page__virtual_page_resource__banner")
     */
    private $banners;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\VirtualPageResource\Factoid", inversedBy="virtualPages")
     * @ORM\JoinTable(name="util_rel.virtual_page__virtual_page_resource__factoid")
     */
    private $factoids;

    public function __construct()
    {
        $this->subVirtualPages = new ArrayCollection();
        $this->bannerShelves = new ArrayCollection();
        $this->productShelves = new ArrayCollection();
        $this->banners = new ArrayCollection();
        $this->factoids = new ArrayCollection();
    }
}
