<?php

namespace App\Entity\ProductProperty;

use App\EntitySupport\Common\BaseEntity;
use App\EntitySupport\Common\BaseEntityTrait;
use App\EntitySupport\Behavior\ChangeTimeSavingBehavior;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use App\Entity\ProductType\Cloth as ProductTypeCloth;
use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ProductProperty\ColorRepository")
 * @ORM\Table(name="product_property.color")
 * @ORM\HasLifecycleCallbacks
 *
 * @method integer getId()
 * @method string getValue()
 * @method DateTime getCreatedAt()
 * @method DateTime getUpdatedAt()
 * @method Collection getClothes()
 *
 * @method $this setValue(string $value)
 * @method $this setCreatedAt(DateTime $createdAt)
 * @method $this setUpdatedAt(DateTime $updatedAt)
 *
 * @method $this addToClothes(ProductTypeCloth $productTypeCloth)
 * @method $this removeFromClothes(ProductTypeCloth $productTypeCloth)
 */
class Color extends BaseEntity
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
    private $value;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\ProductType\Cloth", inversedBy="colors")
     * @ORM\JoinTable(name="util_rel.product_property__color__product_type__cloth")
     */
    private $clothes;

    public function __construct()
    {
        $this->clothes = new ArrayCollection();
    }
}
