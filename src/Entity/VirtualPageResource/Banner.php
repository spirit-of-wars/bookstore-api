<?php

namespace App\Entity\VirtualPageResource;

use App\EntitySupport\Common\BaseEntity;
use App\EntitySupport\Common\BaseEntityTrait;
use App\EntitySupport\Behavior\ChangeTimeSavingBehavior;
use App\EntitySupport\Behavior\ResourceGetterBehavior;
use DateTime;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use App\Entity\VirtualPage;
use App\Entity\Resource;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\VirtualPageResource\BannerRepository")
 * @ORM\Table(name="virtual_page_resource.banner")
 * @ORM\HasLifecycleCallbacks
 *
 * @method integer getId()
 * @method string getName()
 * @method string getType()
 * @method string getLink()
 * @method string getLinkTitle()
 * @method string getDescription()
 * @method integer getFrequency()
 * @method DateTime getDataActiveTo()
 * @method DateTime getDataActiveFrom()
 * @method boolean getIsActiveMiniBanner()
 * @method DateTime getCreatedAt()
 * @method DateTime getUpdatedAt()
 * @method Collection getBannerShelves()
 * @method Collection getVirtualPages()
 * @method Collection getResources()
 *
 * @method $this setName(string $name)
 * @method $this setType(string $type)
 * @method $this setLink(string $link)
 * @method $this setLinkTitle(string $linkTitle)
 * @method $this setDescription(string $description)
 * @method $this setFrequency(integer $frequency)
 * @method $this setDataActiveTo(DateTime $dataActiveTo)
 * @method $this setDataActiveFrom(DateTime $dataActiveFrom)
 * @method $this setIsActiveMiniBanner(boolean $isActiveMiniBanner)
 * @method $this setCreatedAt(DateTime $createdAt)
 * @method $this setUpdatedAt(DateTime $updatedAt)
 *
 * @method $this addToBannerShelves(BannerShelf $bannerShelf)
 * @method $this removeFromBannerShelves(BannerShelf $bannerShelf)
 * @method $this addToVirtualPages(VirtualPage $virtualPage)
 * @method $this removeFromVirtualPages(VirtualPage $virtualPage)
 * @method $this addToResources(Resource $resource)
 * @method $this removeFromResources(Resource $resource)
 */
class Banner extends BaseEntity
{
    use BaseEntityTrait;
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
     * @ORM\Column(type="string", length=255, nullable=false)
     * #App\Constraint:MifName()
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255, nullable=false)
     * #App\Constraint:Enum("BannerType")
     * #App\Field(inForm="inputHidden")
     */
    private $type;

    /**
     * @ORM\Column(type="string", length=255, nullable=false)
     * #App\Constraint:Length(min=10)
     */
    private $link;

    /**
     * @ORM\Column(type="string", length=255, nullable=false)
     * #App\Constraint:Length(min=1)
     */
    private $linkTitle;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * #App\Constraint:Length(min=1)
     */
    private $description;

    /** @ORM\Column(type="integer", nullable=true) */
    private $frequency;

    /** @ORM\Column(type="datetime", nullable=true) */
    private $dataActiveTo;

    /** @ORM\Column(type="datetime", nullable=true) */
    private $dataActiveFrom;

    /** @ORM\Column(type="boolean", nullable=true) */
    private $isActiveMiniBanner;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\VirtualPageResource\BannerShelf", inversedBy="banners")
     * @ORM\JoinTable(name="util_rel.virtual_page_resource__banner__virtual_page_resource__banner_shelf")
     */
    private $bannerShelves;

    /** @ORM\ManyToMany(targetEntity="App\Entity\VirtualPage", mappedBy="banners") */
    private $virtualPages;

    /** @ORM\ManyToMany(targetEntity="App\Entity\Resource", mappedBy="banners") */
    private $resources;

    public function __construct()
    {
        $this->bannerShelves = new ArrayCollection();
        $this->virtualPages = new ArrayCollection();
        $this->resources = new ArrayCollection();
    }
}
