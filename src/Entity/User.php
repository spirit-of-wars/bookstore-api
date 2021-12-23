<?php

namespace App\Entity;

use App\EntitySupport\Common\BaseEntity;
use App\EntitySupport\Common\BaseEntityTrait;
use App\EntitySupport\Behavior\ChangeTimeSavingBehavior;
use App\EntitySupport\Behavior\UserBehavior;
use App\Entity\Auth\AccessToken as AuthAccessToken;
use App\Entity\Auth\RefreshToken as AuthRefreshToken;
use App\Entity\Auth\ConfirmLink as AuthConfirmLink;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use App\Entity\Auth\Role as AuthRole;
use App\Entity\Auth\SocNetworkUserData as AuthSocNetworkUserData;
use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @ORM\Table(name="common.users")
 * @ORM\HasLifecycleCallbacks
 *
 * @method integer getId()
 * @method string getEmail()
 * @method string getFirstname()
 * @method string getSurname()
 * @method integer getSpent()
 * @method boolean getIsConfirmed()
 * @method boolean getIsActive()
 * @method DateTime getCreatedAt()
 * @method DateTime getUpdatedAt()
 * @method AuthAccessToken getAccessToken()
 * @method AuthRefreshToken getRefreshToken()
 * @method AuthConfirmLink getConfirmLink()
 * @method Collection getRoles()
 * @method Collection getSocNetworks()
 *
 * @method $this setEmail(string $email)
 * @method $this setFirstname(string $firstname)
 * @method $this setSurname(string $surname)
 * @method $this setSpent(integer $spent)
 * @method $this setIsConfirmed(boolean $isConfirmed)
 * @method $this setIsActive(boolean $isActive)
 * @method $this setCreatedAt(DateTime $createdAt)
 * @method $this setUpdatedAt(DateTime $updatedAt)
 * @method $this setAccessToken(AuthAccessToken $accessToken)
 * @method $this setRefreshToken(AuthRefreshToken $refreshToken)
 * @method $this setConfirmLink(AuthConfirmLink $confirmLink)
 *
 * @method $this addToRoles(AuthRole $authRole)
 * @method $this removeFromRoles(AuthRole $authRole)
 * @method $this addToSocNetworks(AuthSocNetworkUserData $authSocNetworkUserData)
 * @method $this removeFromSocNetworks(AuthSocNetworkUserData $authSocNetworkUserData)
 */
class User extends BaseEntity
{
    use BaseEntityTrait;
    use ChangeTimeSavingBehavior;
    use UserBehavior;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @ORM\Column(type="integer")
     * #App\Field(inForm="inputHidden")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * #App\Constraint:Email()
     * #App\Constraint:Length(min=6)
     */
    private $email;

    /** @ORM\Column(type="string", length=255, nullable=true) */
    private $firstname;

    /** @ORM\Column(type="string", length=255, nullable=true) */
    private $surname;

    /** @ORM\Column(type="integer", nullable=true) */
    private $spent;

    /** @ORM\Column(type="boolean", nullable=true) */
    private $isConfirmed = false;

    /** @ORM\Column(type="boolean", nullable=true) */
    private $isActive = false;

    /** @ORM\OneToOne(targetEntity="App\Entity\Auth\AccessToken", mappedBy="user") */
    private $accessToken;

    /** @ORM\OneToOne(targetEntity="App\Entity\Auth\RefreshToken", mappedBy="user") */
    private $refreshToken;

    /** @ORM\OneToOne(targetEntity="App\Entity\Auth\ConfirmLink", mappedBy="user") */
    private $confirmLink;

    /** @ORM\ManyToMany(targetEntity="App\Entity\Auth\Role", mappedBy="users") */
    private $roles;

    /** @ORM\OneToMany(targetEntity="App\Entity\Auth\SocNetworkUserData", mappedBy="user") */
    private $socNetworks;

    public function __construct()
    {
        $this->roles = new ArrayCollection();
        $this->socNetworks = new ArrayCollection();
    }
}
