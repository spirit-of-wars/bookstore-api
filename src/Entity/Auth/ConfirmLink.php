<?php

namespace App\Entity\Auth;

use App\EntitySupport\Common\BaseEntity;
use App\EntitySupport\Common\BaseEntityTrait;
use App\EntitySupport\Behavior\ChangeTimeSavingBehavior;
use DateTime;
use App\Entity\User;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Auth\ConfirmLinkRepository")
 * @ORM\Table(name="auth.confirm_link")
 * @ORM\HasLifecycleCallbacks
 *
 * @method integer getId()
 * @method string getToken()
 * @method integer getCode()
 * @method boolean getIsActivated()
 * @method DateTime getExpire()
 * @method DateTime getCreatedAt()
 * @method DateTime getUpdatedAt()
 * @method RefreshToken getRefreshToken()
 * @method AccessToken getAccessToken()
 * @method User getUser()
 *
 * @method $this setToken(string $token)
 * @method $this setCode(integer $code)
 * @method $this setIsActivated(boolean $isActivated)
 * @method $this setExpire(DateTime $expire)
 * @method $this setCreatedAt(DateTime $createdAt)
 * @method $this setUpdatedAt(DateTime $updatedAt)
 * @method $this setRefreshToken(RefreshToken $refreshToken)
 * @method $this setAccessToken(AccessToken $accessToken)
 * @method $this setUser(User $user)
 */
class ConfirmLink extends BaseEntity
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

    /** @ORM\Column(type="integer", nullable=true) */
    private $code;

    /** @ORM\Column(type="boolean", nullable=true) */
    private $isActivated;

    /** @ORM\Column(type="datetime", nullable=true) */
    private $expire;

    /** @ORM\OneToOne(targetEntity="App\Entity\Auth\RefreshToken", inversedBy="confirmLink") */
    private $refreshToken;

    /** @ORM\OneToOne(targetEntity="App\Entity\Auth\AccessToken", inversedBy="confirmLink") */
    private $accessToken;

    /** @ORM\OneToOne(targetEntity="App\Entity\User", inversedBy="confirmLink") */
    private $user;
}
