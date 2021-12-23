<?php

namespace App\Entity;

use App\EntitySupport\Common\BaseEntity;
use App\EntitySupport\Common\BaseEntityTrait;
use App\EntitySupport\Behavior\ChangeTimeSavingBehavior;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use App\Entity\ProductEssence\Book as ProductEssenceBook;
use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CreativePartnerRepository")
 * @ORM\Table(name="common.creative_partner")
 * @ORM\HasLifecycleCallbacks
 *
 * @method integer getId()
 * @method integer getOldModxId()
 * @method string getName()
 * @method string getSlug()
 * @method string getUri()
 * @method DateTime getCreatedAt()
 * @method DateTime getUpdatedAt()
 * @method Resource getLogo()
 * @method Collection getBooks()
 *
 * @method $this setOldModxId(integer $oldModxId)
 * @method $this setName(string $name)
 * @method $this setSlug(string $slug)
 * @method $this setUri(string $uri)
 * @method $this setCreatedAt(DateTime $createdAt)
 * @method $this setUpdatedAt(DateTime $updatedAt)
 * @method $this setLogo(Resource $logo)
 *
 * @method $this addToBooks(ProductEssenceBook $productEssenceBook)
 * @method $this removeFromBooks(ProductEssenceBook $productEssenceBook)
 */
class CreativePartner extends BaseEntity
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
     * #App\Field(inForm="hidden")
     */
    private $oldModxId;

    /**
     * @ORM\Column(type="string", length=255, nullable=false)
     * #App\Constraint:MifName()
     */
    private $name;

    /** @ORM\Column(type="string", length=255, nullable=true) */
    private $slug;

    /** @ORM\Column(type="string", length=255, nullable=true) */
    private $uri;

    /** @ORM\OneToOne(targetEntity="App\Entity\Resource") */
    private $logo;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\ProductEssence\Book", inversedBy="creativePartners")
     * @ORM\JoinTable(name="util_rel.creative_partner__product_essence__book")
     */
    private $books;

    public function __construct()
    {
        $this->books = new ArrayCollection();
    }
}
