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
 * @ORM\Entity(repositoryClass="App\Repository\ProductGroup\SeriesRepository")
 * @ORM\Table(name="product_group.series")
 * @ORM\HasLifecycleCallbacks
 *
 * @method integer getId()
 * @method string getName()
 * @method DateTime getCreatedAt()
 * @method DateTime getUpdatedAt()
 * @method Collection getEssences()
 *
 * @method $this setName(string $name)
 * @method $this setCreatedAt(DateTime $createdAt)
 * @method $this setUpdatedAt(DateTime $updatedAt)
 *
 * @method $this addToEssences(ProductEssenceEssence $productEssenceEssence)
 * @method $this removeFromEssences(ProductEssenceEssence $productEssenceEssence)
 */
class Series extends BaseEntity
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

    /** @ORM\Column(type="string", length=255, nullable=true) */
    private $name;

    /** @ORM\OneToMany(targetEntity="App\Entity\ProductEssence\Essence", mappedBy="series") */
    private $essences;

    public function __construct()
    {
        $this->essences = new ArrayCollection();
    }
}
