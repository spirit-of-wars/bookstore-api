<?php

namespace App\Entity\ProductEssence;

use App\EntitySupport\Common\BaseEntity;
use App\EntitySupport\Common\BaseEntityTrait;
use App\EntitySupport\Behavior\ChangeTimeSavingBehavior;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use App\Entity\CreativePartner;
use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ProductEssence\BookRepository")
 * @ORM\Table(name="product_essence.book")
 * @ORM\HasLifecycleCallbacks
 *
 * @method integer getId()
 * @method string getOriginalPrimaryName()
 * @method string getOriginalSecondaryName()
 * @method array getQuotes()
 * @method mixed getFromQuotes(string $key)
 * @method string getWorkDescription()
 * @method array getPosts()
 * @method mixed getFromPosts(string $key)
 * @method array getTeam()
 * @method mixed getFromTeam(string $key)
 * @method DateTime getCreatedAt()
 * @method DateTime getUpdatedAt()
 * @method Essence getEssence()
 * @method Collection getCreativePartners()
 *
 * @method $this setOriginalPrimaryName(string $originalPrimaryName)
 * @method $this setOriginalSecondaryName(string $originalSecondaryName)
 * @method $this setQuotes(array $quotes)
 * @method $this setWorkDescription(string $workDescription)
 * @method $this setPosts(array $posts)
 * @method $this setTeam(array $team)
 * @method $this setCreatedAt(DateTime $createdAt)
 * @method $this setUpdatedAt(DateTime $updatedAt)
 * @method $this setEssence(Essence $essence)
 *
 * @method $this addToQuotes(string|array $keyOrMap, mixed $value = null)
 * @method $this removeFromQuotes(string $key)
 * @method $this addToPosts(string|array $keyOrMap, mixed $value = null)
 * @method $this removeFromPosts(string $key)
 * @method $this addToTeam(string|array $keyOrMap, mixed $value = null)
 * @method $this removeFromTeam(string $key)
 * @method $this addToCreativePartners(CreativePartner $creativePartner)
 * @method $this removeFromCreativePartners(CreativePartner $creativePartner)
 */
class Book extends BaseEntity
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

    /** @ORM\Column(type="string", length=2048, nullable=true) */
    private $originalPrimaryName;

    /** @ORM\Column(type="string", length=2048, nullable=true) */
    private $originalSecondaryName;

    /** @ORM\Column(type="dict", nullable=true) */
    private $quotes;

    /** @ORM\Column(type="text", nullable=true) */
    private $workDescription;

    /** @ORM\Column(type="dict", nullable=true) */
    private $posts;

    /** @ORM\Column(type="dict", nullable=true) */
    private $team;

    /** @ORM\OneToOne(targetEntity="App\Entity\ProductEssence\Essence") */
    private $essence;

    /** @ORM\ManyToMany(targetEntity="App\Entity\CreativePartner", mappedBy="books") */
    private $creativePartners;

    public function __construct()
    {
        $this->creativePartners = new ArrayCollection();
    }
}
