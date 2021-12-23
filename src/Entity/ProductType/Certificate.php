<?php

namespace App\Entity\ProductType;

use App\EntitySupport\Common\BaseEntity;
use App\EntitySupport\Common\BaseEntityTrait;
use App\EntitySupport\Behavior\ChangeTimeSavingBehavior;
use App\Entity\Product;
use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ProductType\CertificateRepository")
 * @ORM\Table(name="product_type.certificate")
 * @ORM\HasLifecycleCallbacks
 *
 * @method integer getId()
 * @method string getComment()
 * @method array getParameters()
 * @method mixed getFromParameters(string $key)
 * @method DateTime getCreatedAt()
 * @method DateTime getUpdatedAt()
 * @method Product getProduct()
 *
 * @method $this setComment(string $comment)
 * @method $this setParameters(array $parameters)
 * @method $this setCreatedAt(DateTime $createdAt)
 * @method $this setUpdatedAt(DateTime $updatedAt)
 * @method $this setProduct(Product $product)
 *
 * @method $this addToParameters(string|array $keyOrMap, mixed $value = null)
 * @method $this removeFromParameters(string $key)
 */
class Certificate extends BaseEntity
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
    private $comment;

    /** @ORM\Column(type="dict", nullable=true) */
    private $parameters;

    /** @ORM\OneToOne(targetEntity="App\Entity\Product") */
    private $product;
}
