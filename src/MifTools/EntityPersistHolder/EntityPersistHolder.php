<?php

namespace App\MifTools\EntityPersistHolder;

use App\EntitySupport\Common\BaseEntity;
use App\Mif;
use Doctrine\Persistence\ObjectManager;

/**
 * Class EntityPersistHolder
 * @package App\MifTools\EntityPersistHolder
 */
class EntityPersistHolder
{
    /** @var ObjectManager */
    private $entityManager;

    /** @var int */
    private $holdCounter = 0;

    /** @var array */
    private $holdStack = [];

    /** @var array */
    private $stackElems = [];

    /**
     * @return ObjectManager
     */
    public function getEntityManager()
    {
        if (!isset($this->entityManager)) {
            $this->entityManager = Mif::getDoctrine()->getManager();
        }

        return $this->entityManager;
    }

    /**
     * @return void
     */
    public function hold()
    {
        $this->holdCounter++;
        $this->holdStack[] = [];
    }

    /**
     * @return void
     */
    public function commit()
    {
        $list = $this->pop();

        if ($this->holdCounter === 0) {
            $this->stackElems = array_merge($this->stackElems, $list);
            $this->tryFlush();
        } else {
            if (empty($this->holdStack)) {
                $this->holdStack[] = $list;
            } else {
                $index = count($this->holdStack) - 1;
                $this->holdStack[$index] = array_merge($this->holdStack[$index], $list);
            }
        }
    }

    /**
     * @return void
     */
    public function drop()
    {
        $this->pop();
    }

    /**
     * @return array
     */
    public function pop()
    {
        $this->holdCounter--;
        if ($this->holdCounter < 0) {
            $this->holdCounter = 0;
        }

        if (!isset($this->holdStack[$this->holdCounter])) {
            return [];
        }

        $list = array_pop($this->holdStack);
        return $list;
    }

    /**
     * @param BaseEntity $entity
     */
    public function persistEntity(BaseEntity $entity)
    {
        $this->addEntityToStack($entity);
    }

    /**
     * @param BaseEntity $entity
     */
    public function removeEntity(BaseEntity $entity)
    {
        $this->addEntityToStack($entity, true);
    }

    /**
     * @param BaseEntity|null $entity
     * @return bool
     */
    public function tryFlush(BaseEntity $entity = null)
    {

        if ($this->holdCounter > 0) {
            return false;
        }

        $em = $this->getEntityManager();

        if ($entity) {
            $objId = spl_object_hash($entity);
            $stackElem = $this->stackElems[$objId] ?? null;
            if ($stackElem === null) {
                return false;
            }
            $this->applyElemAction($stackElem);
        } else {
            $this->applyElemsAction();
        }

        $em->flush();

        return true;
    }

    /**
     * @param BaseEntity $entity
     * @param bool $isRemove
     */
    private function addEntityToStack(BaseEntity $entity, $isRemove = false)
    {
        $stackElem = new StackElem($entity, $isRemove);
        $objId = $stackElem->getEntityHashId();

        if ($this->holdCounter > 0) {
            $index = count($this->holdStack) - 1;

            $this->holdStack[$index][$objId] = $stackElem;
        } else {
            $this->stackElems[$objId] = $stackElem;
        }

    }

    private function applyElemsAction()
    {
        /** @var StackElem $stackElem */
        foreach ($this->stackElems as $stackElem) {
            $stackElem->applyManagerAction($this->getEntityManager());
        }
        $this->stackElems = [];
    }

    /**
     * @param StackElem $stackElem
     */
    private function applyElemAction(StackElem $stackElem)
    {
        $stackElem->applyManagerAction($this->getEntityManager());
        $objId = $stackElem->getEntityHashId();
        unset($this->stackElems[$objId]);
    }
}
