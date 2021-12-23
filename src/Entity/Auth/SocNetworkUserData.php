<?php

namespace App\Entity\Auth;

use App\EntitySupport\Common\BaseEntity;
use App\EntitySupport\Common\BaseEntityTrait;
use App\EntitySupport\Behavior\ChangeTimeSavingBehavior;
use App\Entity\User;
use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Auth\SocNetworkUserDataRepository")
 * @ORM\Table(name="auth.soc_network_user_data")
 * @ORM\HasLifecycleCallbacks
 *
 * @method integer getId()
 * @method string getCode()
 * @method string getSocNetworkId()
 * @method DateTime getCreatedAt()
 * @method DateTime getUpdatedAt()
 * @method User getUser()
 *
 * @method $this setCode(string $code)
 * @method $this setSocNetworkId(string $socNetworkId)
 * @method $this setCreatedAt(DateTime $createdAt)
 * @method $this setUpdatedAt(DateTime $updatedAt)
 * @method $this setUser(User $user)
 */
class SocNetworkUserData extends BaseEntity
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
     * @ORM\Column(type="string", length=255, nullable=true)
     * #App\Constraint:Enum("CodeSocNetworkEnum")
     */
    private $code;

    /** @ORM\Column(type="string", length=255, nullable=true) */
    private $socNetworkId;

    /** @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="socNetworks") */
    private $user;
}
