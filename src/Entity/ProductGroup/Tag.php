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
 * @ORM\Entity(repositoryClass="App\Repository\ProductGroup\TagRepository")
 * @ORM\Table(name="product_group.tag")
 * @ORM\HasLifecycleCallbacks
 *
 * @method integer getId()
 * @method string getTitle()
 * @method string getSlug()
 * @method string getDescription()
 * @method DateTime getCreatedAt()
 * @method DateTime getUpdatedAt()
 * @method Collection getEssences()
 *
 * @method $this setTitle(string $title)
 * @method $this setSlug(string $slug)
 * @method $this setDescription(string $description)
 * @method $this setCreatedAt(DateTime $createdAt)
 * @method $this setUpdatedAt(DateTime $updatedAt)
 *
 * @method $this addToEssences(ProductEssenceEssence $productEssenceEssence)
 * @method $this removeFromEssences(ProductEssenceEssence $productEssenceEssence)
 */
class Tag extends BaseEntity
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
     * #App\Constraint:Length(min=1)
     */
    private $title;

    /**
     * @ORM\Column(type="string", length=255, nullable=false)
     * #App\Constraint:Length(min=1)
     * #App\Constraint:Slug()
     */
    private $slug;

    /** @ORM\Column(type="text", nullable=true) */
    private $description;

    /** @ORM\ManyToMany(targetEntity="App\Entity\ProductEssence\Essence", mappedBy="tags") */
    private $essences;

    public function __construct()
    {
        $this->essences = new ArrayCollection();
    }
}
