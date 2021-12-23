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
 * @ORM\Entity(repositoryClass="App\Repository\ProductProperty\SizeRepository")
 * @ORM\Table(name="product_property.size")
 * @ORM\HasLifecycleCallbacks
 *
 * @method integer getId()
 * @method string getValueRu()
 * @method string getValueEu()
 * @method DateTime getCreatedAt()
 * @method DateTime getUpdatedAt()
 * @method Collection getClothes()
 *
 * @method $this setValueRu(string $valueRu)
 * @method $this setValueEu(string $valueEu)
 * @method $this setCreatedAt(DateTime $createdAt)
 * @method $this setUpdatedAt(DateTime $updatedAt)
 *
 * @method $this addToClothes(ProductTypeCloth $productTypeCloth)
 * @method $this removeFromClothes(ProductTypeCloth $productTypeCloth)
 */
class Size extends BaseEntity
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
    private $valueRu;

    /** @ORM\Column(type="string", length=255, nullable=true) */
    private $valueEu;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\ProductType\Cloth", inversedBy="sizes")
     * @ORM\JoinTable(name="util_rel.product_property__size__product_type__cloth")
     */
    private $clothes;

    public function __construct()
    {
        $this->clothes = new ArrayCollection();
    }
}
