<?php

namespace App\Entity\VirtualPageResource;

use App\EntitySupport\Common\BaseEntity;
use App\EntitySupport\Common\BaseEntityTrait;
use App\EntitySupport\Behavior\ChangeTimeSavingBehavior;
use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\VirtualPageResource\FreeBlockRepository")
 * @ORM\Table(name="virtual_page_resource.free_block")
 * @ORM\HasLifecycleCallbacks
 *
 * @method integer getId()
 * @method string getTitle()
 * @method string getText()
 * @method DateTime getCreatedAt()
 * @method DateTime getUpdatedAt()
 *
 * @method $this setTitle(string $title)
 * @method $this setText(string $text)
 * @method $this setCreatedAt(DateTime $createdAt)
 * @method $this setUpdatedAt(DateTime $updatedAt)
 */
class FreeBlock extends BaseEntity
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
    private $title;

    /** @ORM\Column(type="text", nullable=true) */
    private $text;
}
