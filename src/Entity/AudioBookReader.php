<?php

namespace App\Entity;

use App\EntitySupport\Common\BaseEntity;
use App\EntitySupport\Common\BaseEntityTrait;
use App\EntitySupport\Behavior\ChangeTimeSavingBehavior;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use App\Entity\ProductType\AudioBook as ProductTypeAudioBook;
use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\AudioBookReaderRepository")
 * @ORM\Table(name="common.audio_book_reader")
 * @ORM\HasLifecycleCallbacks
 *
 * @method integer getId()
 * @method string getName()
 * @method string getEnName()
 * @method string getDescription()
 * @method string getSlug()
 * @method string getUri()
 * @method DateTime getCreatedAt()
 * @method DateTime getUpdatedAt()
 * @method Collection getAudioBooks()
 *
 * @method $this setName(string $name)
 * @method $this setEnName(string $enName)
 * @method $this setDescription(string $description)
 * @method $this setSlug(string $slug)
 * @method $this setUri(string $uri)
 * @method $this setCreatedAt(DateTime $createdAt)
 * @method $this setUpdatedAt(DateTime $updatedAt)
 *
 * @method $this addToAudioBooks(ProductTypeAudioBook $productTypeAudioBook)
 * @method $this removeFromAudioBooks(ProductTypeAudioBook $productTypeAudioBook)
 */
class AudioBookReader extends BaseEntity
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
     * #App\Constraint:MifName()
     */
    private $name;

    /** @ORM\Column(type="string", length=255, nullable=true) */
    private $enName;

    /** @ORM\Column(type="text", nullable=true) */
    private $description;

    /** @ORM\Column(type="text", nullable=true) */
    private $slug;

    /** @ORM\Column(type="text", nullable=true) */
    private $uri;

    /** @ORM\OneToMany(targetEntity="App\Entity\ProductType\AudioBook", mappedBy="reader") */
    private $audioBooks;

    public function __construct()
    {
        $this->audioBooks = new ArrayCollection();
    }
}
