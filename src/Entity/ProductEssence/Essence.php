<?php

namespace App\Entity\ProductEssence;

use App\EntitySupport\Common\BaseEntity;
use App\EntitySupport\Common\BaseEntityTrait;
use App\EntitySupport\Behavior\ChangeTimeSavingBehavior;
use App\EntitySupport\Behavior\ResourceGetterBehavior;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use App\Entity\Product;
use App\Entity\ProductGroup\Category as ProductGroupCategory;
use App\Entity\ProductGroup\Tag as ProductGroupTag;
use App\Entity\ProductGroup\Series as ProductGroupSeries;
use App\Entity\Author;
use App\Entity\Resource;
use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ProductEssence\EssenceRepository")
 * @ORM\Table(name="product_essence.essence")
 * @ORM\HasLifecycleCallbacks
 *
 * @method integer getId()
 * @method string getFullName()
 * @method string getPrimaryName()
 * @method string getSecondaryName()
 * @method string getDescription()
 * @method string getFullDescription()
 * @method array getRecommendations()
 * @method mixed getFromRecommendations(string $key)
 * @method array getPromoStickers()
 * @method mixed getFromPromoStickers(string $key)
 * @method DateTime getCreatedAt()
 * @method DateTime getUpdatedAt()
 * @method Collection getProducts()
 * @method Collection getSameEssences()
 * @method Collection getEssencesWithMe()
 * @method ProductGroupCategory getCategory()
 * @method Collection getTags()
 * @method ProductGroupSeries getSeries()
 * @method Author getAuthor()
 * @method Collection getCoAuthor()
 * @method Collection getResources()
 *
 * @method $this setFullName(string $fullName)
 * @method $this setPrimaryName(string $primaryName)
 * @method $this setSecondaryName(string $secondaryName)
 * @method $this setDescription(string $description)
 * @method $this setFullDescription(string $fullDescription)
 * @method $this setRecommendations(array $recommendations)
 * @method $this setPromoStickers(array $promoStickers)
 * @method $this setCreatedAt(DateTime $createdAt)
 * @method $this setUpdatedAt(DateTime $updatedAt)
 * @method $this setCategory(ProductGroupCategory $category)
 * @method $this setSeries(ProductGroupSeries $series)
 * @method $this setAuthor(Author $author)
 *
 * @method $this addToRecommendations(string|array $keyOrMap, mixed $value = null)
 * @method $this removeFromRecommendations(string $key)
 * @method $this addToPromoStickers(string|array $keyOrMap, mixed $value = null)
 * @method $this removeFromPromoStickers(string $key)
 * @method $this addToProducts(Product $product)
 * @method $this removeFromProducts(Product $product)
 * @method $this addToSameEssences(Essence $essence)
 * @method $this removeFromSameEssences(Essence $essence)
 * @method $this addToEssencesWithMe(Essence $essence)
 * @method $this removeFromEssencesWithMe(Essence $essence)
 * @method $this addToTags(ProductGroupTag $productGroupTag)
 * @method $this removeFromTags(ProductGroupTag $productGroupTag)
 * @method $this addToCoAuthor(Author $author)
 * @method $this removeFromCoAuthor(Author $author)
 * @method $this addToResources(Resource $resource)
 * @method $this removeFromResources(Resource $resource)
 */
class Essence extends BaseEntity
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
    private $fullName;

    /**
     * @ORM\Column(type="string", length=255, nullable=false)
     * #App\Constraint:MifName()
     */
    private $primaryName;

    /** @ORM\Column(type="string", length=255, nullable=true) */
    private $secondaryName;

    /** @ORM\Column(type="text", nullable=true) */
    private $description;

    /** @ORM\Column(type="text", nullable=true) */
    private $fullDescription;

    /** @ORM\Column(type="dict", nullable=true) */
    private $recommendations;

    /** @ORM\Column(type="dict", nullable=true) */
    private $promoStickers;

    /** @ORM\OneToMany(targetEntity="App\Entity\Product", mappedBy="essence") */
    private $products;

    /** @ORM\ManyToMany(targetEntity="App\Entity\ProductEssence\Essence", mappedBy="essencesWithMe") */
    private $sameEssences;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\ProductEssence\Essence", inversedBy="sameEssences")
     * @ORM\JoinTable(name="util_rel.product_essence__essence__essences_with_me__same_essences",
     *     joinColumns={@ORM\JoinColumn(name="essences_with_me_id", referencedColumnName="id")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="same_essences_id", referencedColumnName="id")}
     * )
     */
    private $essencesWithMe;

    /** @ORM\ManyToOne(targetEntity="App\Entity\ProductGroup\Category", inversedBy="essences") */
    private $category;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\ProductGroup\Tag", inversedBy="essences")
     * @ORM\JoinTable(name="util_rel.product_essence__essence__product_group__tag")
     */
    private $tags;

    /** @ORM\ManyToOne(targetEntity="App\Entity\ProductGroup\Series", inversedBy="essences") */
    private $series;

    /** @ORM\ManyToOne(targetEntity="App\Entity\Author", inversedBy="selfProducts") */
    private $author;

    /** @ORM\ManyToMany(targetEntity="App\Entity\Author", mappedBy="coAuthoredProducts") */
    private $coAuthor;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Resource", inversedBy="essences")
     * @ORM\JoinTable(name="util_rel.product_essence__essence__resource")
     */
    private $resources;

    public function __construct()
    {
        $this->products = new ArrayCollection();
        $this->sameEssences = new ArrayCollection();
        $this->essencesWithMe = new ArrayCollection();
        $this->tags = new ArrayCollection();
        $this->coAuthor = new ArrayCollection();
        $this->resources = new ArrayCollection();
    }
}
