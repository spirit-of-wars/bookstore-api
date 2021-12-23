<?php

namespace App\Entity\ProductData;

use App\EntitySupport\Common\BaseEntity;
use App\EntitySupport\Common\BaseEntityTrait;
use App\EntitySupport\Behavior\ChangeTimeSavingBehavior;
use App\Entity\Product;
use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ProductData\PromoDataRepository")
 * @ORM\Table(name="product_data.promo_data")
 * @ORM\HasLifecycleCallbacks
 *
 * @method integer getId()
 * @method string getSendDescription()
 * @method string getPassageTitle()
 * @method string getPassageFile()
 * @method string getMetaTagDescription()
 * @method string getMetaTagKeywords()
 * @method string getMetaTagOgImage()
 * @method DateTime getCreatedAt()
 * @method DateTime getUpdatedAt()
 * @method Product getProduct()
 *
 * @method $this setSendDescription(string $sendDescription)
 * @method $this setPassageTitle(string $passageTitle)
 * @method $this setPassageFile(string $passageFile)
 * @method $this setMetaTagDescription(string $metaTagDescription)
 * @method $this setMetaTagKeywords(string $metaTagKeywords)
 * @method $this setMetaTagOgImage(string $metaTagOgImage)
 * @method $this setCreatedAt(DateTime $createdAt)
 * @method $this setUpdatedAt(DateTime $updatedAt)
 * @method $this setProduct(Product $product)
 */
class PromoData extends BaseEntity
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

    /** @ORM\Column(type="text", nullable=true) */
    private $sendDescription;

    /** @ORM\Column(type="string", length=2048, nullable=true) */
    private $passageTitle;

    /** @ORM\Column(type="string", length=2048, nullable=true) */
    private $passageFile;

    /** @ORM\Column(type="text", nullable=true) */
    private $metaTagDescription;

    /** @ORM\Column(type="text", nullable=true) */
    private $metaTagKeywords;

    /** @ORM\Column(type="string", length=255, nullable=true) */
    private $metaTagOgImage;

    /** @ORM\OneToOne(targetEntity="App\Entity\Product", inversedBy="promoData") */
    private $product;
}
