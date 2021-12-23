<?php

namespace App\Entity\ProductType;

use App\EntitySupport\Common\BaseEntity;
use App\EntitySupport\Common\BaseEntityTrait;
use App\EntitySupport\Behavior\ChangeTimeSavingBehavior;
use DateTime;
use App\Entity\Product;
use App\Entity\AudioBookReader;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ProductType\AudioBookRepository")
 * @ORM\Table(name="product_type.audio_book")
 * @ORM\HasLifecycleCallbacks
 *
 * @method integer getId()
 * @method string getDuration()
 * @method DateTime getRightsExpiration()
 * @method DateTime getCreatedAt()
 * @method DateTime getUpdatedAt()
 * @method Product getProduct()
 * @method AudioBookReader getReader()
 *
 * @method $this setDuration(string $duration)
 * @method $this setRightsExpiration(DateTime $rightsExpiration)
 * @method $this setCreatedAt(DateTime $createdAt)
 * @method $this setUpdatedAt(DateTime $updatedAt)
 * @method $this setProduct(Product $product)
 * @method $this setReader(AudioBookReader $reader)
 */
class AudioBook extends BaseEntity
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
    private $duration;

    /** @ORM\Column(type="datetime", nullable=true) */
    private $rightsExpiration;

    /** @ORM\OneToOne(targetEntity="App\Entity\Product") */
    private $product;

    /** @ORM\ManyToOne(targetEntity="App\Entity\AudioBookReader", inversedBy="audioBooks") */
    private $reader;
}
