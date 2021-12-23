<?php

namespace App\EntitySupport\Common;

use App\Mif;
use App\Service\Entity\EntityService;
use Psr\Container\ContainerInterface;

/**
 * Class TempEntityService
 * @package App\EntitySupport\Common
 */
class TempEntityService extends EntityService
{
    public function __construct($entityClassName)
    {
        parent::__construct(Mif::getServiceProvider()->getContainer());
        $this->entityClassName = $entityClassName;
    }

    /**
     * @return string
     */
    public function getEntityClassName()
    {
        return $this->entityClassName;
    }
}
