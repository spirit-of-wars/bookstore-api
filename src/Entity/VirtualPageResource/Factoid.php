<?php

namespace App\Entity\VirtualPageResource;

use App\EntitySupport\Common\BaseEntity;
use App\EntitySupport\Common\BaseEntityTrait;
use App\EntitySupport\Behavior\ChangeTimeSavingBehavior;
use App\EntitySupport\Behavior\ResourceGetterBehavior;
use App\Entity\Resource;
use App\Entity\Product;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use App\Entity\VirtualPage;
use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\VirtualPageResource\FactoidRepository")
 * @ORM\Table(name="virtual_page_resource.factoid")
 * @ORM\HasLifecycleCallbacks
 *
 * @method integer getId()
 * @method string getName()
 * @method string getCode()
 * @method string getDescription()
 * @method string getType()
 * @method DateTime getCreatedAt()
 * @method DateTime getUpdatedAt()
 * @method Resource getImage()
 * @method Product getProduct()
 * @method Collection getVirtualPages()
 *
 * @method $this setName(string $name)
 * @method $this setCode(string $code)
 * @method $this setDescription(string $description)
 * @method $this setType(string $type)
 * @method $this setCreatedAt(DateTime $createdAt)
 * @method $this setUpdatedAt(DateTime $updatedAt)
 * @method $this setImage(Resource $image)
 * @method $this setProduct(Product $product)
 *
 * @method $this addToVirtualPages(VirtualPage $virtualPage)
 * @method $this removeFromVirtualPages(VirtualPage $virtualPage)
 */
class Factoid extends BaseEntity
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
     * #App\Constraint:Length(min=1)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255, nullable=false)
     * #App\Constraint:Length(min=1)
     */
    private $code;

    /**
     * @ORM\Column(type="string", length=255, nullable=false)
     * #App\Constraint:Length(min=1)
     */
    private $description;

    /** @ORM\Column(type="string", length=255, nullable=false) */
    private $type = "common";

    /** @ORM\OneToOne(targetEntity="App\Entity\Resource") */
    private $image;

    /** @ORM\OneToOne(targetEntity="App\Entity\Product", inversedBy="factoid") */
    private $product;

    /** @ORM\ManyToMany(targetEntity="App\Entity\VirtualPage", mappedBy="factoids") */
    private $virtualPages;

    public function __construct()
    {
        $this->virtualPages = new ArrayCollection();
    }
}
