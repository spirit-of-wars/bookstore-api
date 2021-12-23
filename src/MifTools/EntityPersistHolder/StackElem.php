<?php

namespace App\MifTools\EntityPersistHolder;

use App\EntitySupport\Common\BaseEntity;
use Doctrine\Persistence\ObjectManager;

class StackElem
{
    /**
     * @var BaseEntity
     */
    private $entity;

    /**
     * @var string
     */
    private $type;

    /**
     * @var string
     */
    private $entityHashId;

    const TYPE_REMOVE = 'remove';
    const TYPE_PERSIST = 'persist';

    /**
     * StackElem constructor.
     * @param BaseEntity $entity
     * @param bool $isRemove
     */
    public function __construct(BaseEntity $entity, $isRemove = false)
    {
        $this->entity = $entity;
        $this->type = $isRemove ? self::TYPE_REMOVE : self::TYPE_PERSIST;
        $this->entityHashId = spl_object_hash($entity);
    }

    /**
     * @return BaseEntity
     */
    public function getEntity()
    {
        return $this->entity;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function getEntityHashId()
    {
        return $this->entityHashId;
    }

    /**
     * @return bool
     */
    public function isRemove()
    {
        return $this->type === self::TYPE_REMOVE;
    }

    /**
     * @return bool
     */
    public function isPersist()
    {
        return $this->type === self::TYPE_PERSIST;
    }

    /**
     * @param ObjectManager $entityManager
     */
    public function applyManagerAction($entityManager)
    {
        $entity = $this->getEntity();
        if ($this->isPersist()) {
            $entityManager->persist($entity);
        } elseif ($this->isRemove()) {
            $entityManager->remove($entity);
        }
    }
}
