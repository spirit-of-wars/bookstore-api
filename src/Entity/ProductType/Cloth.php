<?php

namespace App\Entity\ProductType;

use App\EntitySupport\Common\BaseEntity;
use App\EntitySupport\Common\BaseEntityTrait;
use App\EntitySupport\Behavior\ChangeTimeSavingBehavior;
use App\Entity\Product;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use App\Entity\ProductProperty\Color as ProductPropertyColor;
use App\Entity\ProductProperty\Size as ProductPropertySize;
use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ProductType\ClothRepository")
 * @ORM\Table(name="product_type.cloth")
 * @ORM\HasLifecycleCallbacks
 *
 * @method integer getId()
 * @method string getBox()
 * @method string getMaterial()
 * @method string getMaterialContent()
 * @method DateTime getCreatedAt()
 * @method DateTime getUpdatedAt()
 * @method Product getProduct()
 * @method Collection getColors()
 * @method Collection getSizes()
 *
 * @method $this setBox(string $box)
 * @method $this setMaterial(string $material)
 * @method $this setMaterialContent(string $materialContent)
 * @method $this setCreatedAt(DateTime $createdAt)
 * @method $this setUpdatedAt(DateTime $updatedAt)
 * @method $this setProduct(Product $product)
 *
 * @method $this addToColors(ProductPropertyColor $productPropertyColor)
 * @method $this removeFromColors(ProductPropertyColor $productPropertyColor)
 * @method $this addToSizes(ProductPropertySize $productPropertySize)
 * @method $this removeFromSizes(ProductPropertySize $productPropertySize)
 */
class Cloth extends BaseEntity
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
    private $box;

    /** @ORM\Column(type="string", length=255, nullable=true) */
    private $material;

    /** @ORM\Column(type="string", length=255, nullable=true) */
    private $materialContent;

    /** @ORM\OneToOne(targetEntity="App\Entity\Product") */
    private $product;

    /** @ORM\ManyToMany(targetEntity="App\Entity\ProductProperty\Color", mappedBy="clothes") */
    private $colors;

    /** @ORM\ManyToMany(targetEntity="App\Entity\ProductProperty\Size", mappedBy="clothes") */
    private $sizes;

    public function __construct()
    {
        $this->colors = new ArrayCollection();
        $this->sizes = new ArrayCollection();
    }
}
