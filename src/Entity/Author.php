<?php

namespace App\Entity;

use App\EntitySupport\Common\BaseEntity;
use App\EntitySupport\Common\BaseEntityTrait;
use App\EntitySupport\Behavior\ChangeTimeSavingBehavior;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use App\Entity\ProductEssence\Essence as ProductEssenceEssence;
use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\AuthorRepository")
 * @ORM\Table(name="common.author")
 * @ORM\HasLifecycleCallbacks
 *
 * @method integer getId()
 * @method integer getOldModxId()
 * @method string getName()
 * @method string getEnName()
 * @method string getDescription()
 * @method string getSlug()
 * @method string getUri()
 * @method DateTime getCreatedAt()
 * @method DateTime getUpdatedAt()
 * @method Collection getSelfProducts()
 * @method Collection getCoAuthoredProducts()
 *
 * @method $this setOldModxId(integer $oldModxId)
 * @method $this setName(string $name)
 * @method $this setEnName(string $enName)
 * @method $this setDescription(string $description)
 * @method $this setSlug(string $slug)
 * @method $this setUri(string $uri)
 * @method $this setCreatedAt(DateTime $createdAt)
 * @method $this setUpdatedAt(DateTime $updatedAt)
 *
 * @method $this addToSelfProducts(ProductEssenceEssence $productEssenceEssence)
 * @method $this removeFromSelfProducts(ProductEssenceEssence $productEssenceEssence)
 * @method $this addToCoAuthoredProducts(ProductEssenceEssence $productEssenceEssence)
 * @method $this removeFromCoAuthoredProducts(ProductEssenceEssence $productEssenceEssence)
 */
class Author extends BaseEntity
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
    private $enName;

    /** @ORM\Column(type="text", nullable=true) */
    private $description;

    /** @ORM\Column(type="text", nullable=true) */
    private $slug;

    /** @ORM\Column(type="text", nullable=true) */
    private $uri;

    /** @ORM\OneToMany(targetEntity="App\Entity\ProductEssence\Essence", mappedBy="author") */
    private $selfProducts;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\ProductEssence\Essence", inversedBy="coAuthor")
     * @ORM\JoinTable(name="util_rel.author__product_essence__essence")
     */
    private $coAuthoredProducts;

    public function __construct()
    {
        $this->selfProducts = new ArrayCollection();
        $this->coAuthoredProducts = new ArrayCollection();
    }
}
