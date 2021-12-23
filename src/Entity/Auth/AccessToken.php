<?php

namespace App\Entity\Auth;

use App\EntitySupport\Common\BaseEntity;
use App\EntitySupport\Common\BaseEntityTrait;
use App\EntitySupport\Behavior\ChangeTimeSavingBehavior;
use DateTime;
use App\Entity\User;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Auth\AccessTokenRepository")
 * @ORM\Table(name="auth.access_token")
 * @ORM\HasLifecycleCallbacks
 *
 * @method integer getId()
 * @method string getToken()
 * @method DateTime getActivatedAt()
 * @method DateTime getExpire()
 * @method DateTime getCreatedAt()
 * @method DateTime getUpdatedAt()
 * @method ConfirmLink getConfirmLink()
 * @method User getUser()
 *
 * @method $this setToken(string $token)
 * @method $this setActivatedAt(DateTime $activatedAt)
 * @method $this setExpire(DateTime $expire)
 * @method $this setCreatedAt(DateTime $createdAt)
 * @method $this setUpdatedAt(DateTime $updatedAt)
 * @method $this setConfirmLink(ConfirmLink $confirmLink)
 * @method $this setUser(User $user)
 */
class AccessToken extends BaseEntity
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
    private $token;

    /** @ORM\Column(type="datetime", nullable=true) */
    private $activatedAt;

    /** @ORM\Column(type="datetime", nullable=true) */
    private $expire;

    /** @ORM\OneToOne(targetEntity="App\Entity\Auth\ConfirmLink", mappedBy="accessToken") */
    private $confirmLink;

    /** @ORM\OneToOne(targetEntity="App\Entity\User", inversedBy="accessToken") */
    private $user;
}
