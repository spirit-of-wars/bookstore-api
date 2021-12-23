<?php

namespace App\Entity\ProductGroup;

use App\EntitySupport\Common\BaseEntity;
use App\EntitySupport\Common\BaseEntityTrait;
use App\EntitySupport\Behavior\ChangeTimeSavingBehavior;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use App\Entity\Product;
use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ProductGroup\PromoTagRepository")
 * @ORM\Table(name="product_group.promo_tag")
 * @ORM\HasLifecycleCallbacks
 *
 * @method integer getId()
 * @method integer getOldModxId()
 * @method string getPrimaryName()
 * @method string getSecondaryName()
 * @method string getSlug()
 * @method string getUri()
 * @method DateTime getCreatedAt()
 * @method DateTime getUpdatedAt()
 * @method Collection getProducts()
 *
 * @method $this setOldModxId(integer $oldModxId)
 * @method $this setPrimaryName(string $primaryName)
 * @method $this setSecondaryName(string $secondaryName)
 * @method $this setSlug(string $slug)
 * @method $this setUri(string $uri)
 * @method $this setCreatedAt(DateTime $createdAt)
 * @method $this setUpdatedAt(DateTime $updatedAt)
 *
 * @method $this addToProducts(Product $product)
 * @method $this removeFromProducts(Product $product)
 */
class PromoTag extends BaseEntity
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
     * @ORM\Column(type="string", length=255, nullable=true)
     * #App\Constraint:Length(min=1)
     */
    private $primaryName;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * #App\Constraint:Length(min=1)
     */
    private $secondaryName;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * #App\Constraint:Length(min=1)
     * #App\Constraint:Slug()
     */
    private $slug;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * #App\Constraint:Length(min=4)
     * #App\Constraint:Slug()
     */
    private $uri;

    /** @ORM\ManyToMany(targetEntity="App\Entity\Product", mappedBy="promoTags") */
    private $products;

    public function __construct()
    {
        $this->products = new ArrayCollection();
    }
}
