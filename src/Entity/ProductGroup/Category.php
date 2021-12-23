<?php

namespace App\Entity\ProductGroup;

use App\EntitySupport\Common\BaseEntity;
use App\EntitySupport\Common\BaseEntityTrait;
use App\EntitySupport\Behavior\ChangeTimeSavingBehavior;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use App\Entity\ProductEssence\Essence as ProductEssenceEssence;
use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ProductGroup\CategoryRepository")
 * @ORM\Table(name="product_group.category")
 * @ORM\HasLifecycleCallbacks
 *
 * @method integer getId()
 * @method string getName()
 * @method DateTime getCreatedAt()
 * @method DateTime getUpdatedAt()
 * @method Collection getSubCategories()
 * @method Category getParentCategory()
 * @method Collection getEssences()
 *
 * @method $this setName(string $name)
 * @method $this setCreatedAt(DateTime $createdAt)
 * @method $this setUpdatedAt(DateTime $updatedAt)
 * @method $this setParentCategory(Category $parentCategory)
 *
 * @method $this addToSubCategories(Category $category)
 * @method $this removeFromSubCategories(Category $category)
 * @method $this addToEssences(ProductEssenceEssence $productEssenceEssence)
 * @method $this removeFromEssences(ProductEssenceEssence $productEssenceEssence)
 */
class Category extends BaseEntity
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

    /** @ORM\OneToMany(targetEntity="App\Entity\ProductGroup\Category", mappedBy="parentCategory") */
    private $subCategories;

    /** @ORM\ManyToOne(targetEntity="App\Entity\ProductGroup\Category", inversedBy="subCategories") */
    private $parentCategory;

    /** @ORM\OneToMany(targetEntity="App\Entity\ProductEssence\Essence", mappedBy="category") */
    private $essences;

    public function __construct()
    {
        $this->subCategories = new ArrayCollection();
        $this->essences = new ArrayCollection();
    }
}
