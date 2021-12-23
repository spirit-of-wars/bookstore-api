<?php

namespace App\Entity\VirtualPageResource;

use App\EntitySupport\Common\BaseEntity;
use App\EntitySupport\Common\BaseEntityTrait;
use App\EntitySupport\Behavior\ChangeTimeSavingBehavior;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use App\Entity\VirtualPage;
use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\VirtualPageResource\BannerShelfRepository")
 * @ORM\Table(name="virtual_page_resource.banner_shelf")
 * @ORM\HasLifecycleCallbacks
 *
 * @method integer getId()
 * @method string getName()
 * @method string getType()
 * @method string getCode()
 * @method DateTime getCreatedAt()
 * @method DateTime getUpdatedAt()
 * @method Collection getBanners()
 * @method Collection getVirtualPages()
 *
 * @method $this setName(string $name)
 * @method $this setType(string $type)
 * @method $this setCode(string $code)
 * @method $this setCreatedAt(DateTime $createdAt)
 * @method $this setUpdatedAt(DateTime $updatedAt)
 *
 * @method $this addToBanners(Banner $banner)
 * @method $this removeFromBanners(Banner $banner)
 * @method $this addToVirtualPages(VirtualPage $virtualPage)
 * @method $this removeFromVirtualPages(VirtualPage $virtualPage)
 */
class BannerShelf extends BaseEntity
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
     * #App\Constraint:MifName()
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255, nullable=false)
     * #App\Constraint:Enum("BannerShelfType")
     */
    private $type;

    /**
     * @ORM\Column(type="string", length=255, nullable=false)
     * #App\Constraint:Length(min=1)
     */
    private $code;

    /** @ORM\ManyToMany(targetEntity="App\Entity\VirtualPageResource\Banner", mappedBy="bannerShelves") */
    private $banners;

    /** @ORM\ManyToMany(targetEntity="App\Entity\VirtualPage", mappedBy="bannerShelves") */
    private $virtualPages;

    public function __construct()
    {
        $this->banners = new ArrayCollection();
        $this->virtualPages = new ArrayCollection();
    }
}
