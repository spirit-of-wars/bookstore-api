<?php

namespace App\Entity\ProductType;

use App\EntitySupport\Common\BaseEntity;
use App\EntitySupport\Common\BaseEntityTrait;
use App\EntitySupport\Behavior\ChangeTimeSavingBehavior;
use DateTime;
use App\Entity\Product;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ProductType\PaperBookRepository")
 * @ORM\Table(name="product_type.paper_book")
 * @ORM\HasLifecycleCallbacks
 *
 * @method integer getId()
 * @method integer getPriceLabyrinth()
 * @method string getLinkLabyrinth()
 * @method string getLinkOzon()
 * @method string getLinkKnigaBiz()
 * @method DateTime getRightsExpiration()
 * @method DateTime getCreatedAt()
 * @method DateTime getUpdatedAt()
 * @method Product getProduct()
 *
 * @method $this setPriceLabyrinth(integer $priceLabyrinth)
 * @method $this setLinkLabyrinth(string $linkLabyrinth)
 * @method $this setLinkOzon(string $linkOzon)
 * @method $this setLinkKnigaBiz(string $linkKnigaBiz)
 * @method $this setRightsExpiration(DateTime $rightsExpiration)
 * @method $this setCreatedAt(DateTime $createdAt)
 * @method $this setUpdatedAt(DateTime $updatedAt)
 * @method $this setProduct(Product $product)
 */
class PaperBook extends BaseEntity
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

    /** @ORM\Column(type="integer", nullable=true) */
    private $priceLabyrinth;

    /** @ORM\Column(type="string", length=255, nullable=true) */
    private $linkLabyrinth;

    /** @ORM\Column(type="string", length=255, nullable=true) */
    private $linkOzon;

    /** @ORM\Column(type="string", length=255, nullable=true) */
    private $linkKnigaBiz;

    /** @ORM\Column(type="datetime", nullable=true) */
    private $rightsExpiration;

    /** @ORM\OneToOne(targetEntity="App\Entity\Product") */
    private $product;
}
