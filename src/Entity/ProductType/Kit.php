<?php

namespace App\Entity\ProductType;

use App\EntitySupport\Common\BaseEntity;
use App\EntitySupport\Common\BaseEntityTrait;
use App\EntitySupport\Behavior\ChangeTimeSavingBehavior;
use App\Entity\Product;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ProductType\KitRepository")
 * @ORM\Table(name="product_type.kit")
 * @ORM\HasLifecycleCallbacks
 *
 * @method integer getId()
 * @method array getContent()
 * @method mixed getFromContent(string $key)
 * @method DateTime getCreatedAt()
 * @method DateTime getUpdatedAt()
 * @method Product getProduct()
 * @method Collection getItems()
 *
 * @method $this setContent(array $content)
 * @method $this setCreatedAt(DateTime $createdAt)
 * @method $this setUpdatedAt(DateTime $updatedAt)
 * @method $this setProduct(Product $product)
 *
 * @method $this addToContent(string|array $keyOrMap, mixed $value = null)
 * @method $this removeFromContent(string $key)
 * @method $this addToItems(KitItem $kitItem)
 * @method $this removeFromItems(KitItem $kitItem)
 */
class Kit extends BaseEntity
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

    /** @ORM\Column(type="dict", nullable=true) */
    private $content;

    /** @ORM\OneToOne(targetEntity="App\Entity\Product") */
    private $product;

    /** @ORM\OneToMany(targetEntity="App\Entity\ProductType\KitItem", mappedBy="kit") */
    private $items;

    public function __construct()
    {
        $this->items = new ArrayCollection();
    }
}
