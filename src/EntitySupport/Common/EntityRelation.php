<?php

namespace App\EntitySupport\Common;

use App\Enum\OrmRelationTypeEnum;
use App\Helper\NamespaceHelper;
use App\Mif;
use Doctrine\Persistence\ObjectRepository;

/**
 * Class EntityRelation
 * @package App\Entity\Common
 */
class EntityRelation extends EntityProperty
{
    /** @var string */
    private $type;

    /** @var string */
    private $targetEntityClass;

    /** @var string */
    private $targetEntityName;

    /** @var bool|null */
    private $isInversed;

    /** @var string */
    private $targetRelationName;

    /** @var string */
    private $joinTable;

    /**
     * EntityRelation constructor.
     * @param string $name
     * @param array $definition
     */
    public function __construct($name, $definition)
    {
        parent::__construct($name);

        foreach ($definition as $key => $item) {
            if ($key == 'ORM\JoinTable') {
                $this->joinTable = $item['name'];
                continue;
            }

            switch ($key) {
                case 'ORM\OneToOne':
                    $this->type = OrmRelationTypeEnum::ONE_TO_ONE;
                    $this->init($item);
                    break;
                case 'ORM\ManyToOne':
                    $this->type = OrmRelationTypeEnum::MANY_TO_ONE;
                    $this->init($item);
                    break;
                case 'ORM\OneToMany':
                    $this->type = OrmRelationTypeEnum::ONE_TO_MANY;
                    $this->init($item);
                    break;
                case 'ORM\ManyToMany':
                    $this->type = OrmRelationTypeEnum::MANY_TO_MANY;
                    $this->init($item);
                    break;
            }
        }
    }

    /**
     * @return bool
     */
    public function isRelation()
    {
        return true;
    }

    /**
     * @param string $type
     * @return bool
     */
    public function isType($type)
    {
        return $this->type == $type;
    }

    /**
     * @return bool
     */
    public function isOneToOne()
    {
        return $this->isType(OrmRelationTypeEnum::ONE_TO_ONE);
    }

    /**
     * @return bool
     */
    public function isOneToMany()
    {
        return $this->isType(OrmRelationTypeEnum::ONE_TO_MANY);
    }

    /**
     * @return bool
     */
    public function isManyToOne()
    {
        return $this->isType(OrmRelationTypeEnum::MANY_TO_ONE);
    }

    /**
     * @return bool
     */
    public function isManyToMany()
    {
        return $this->isType(OrmRelationTypeEnum::MANY_TO_MANY);
    }

    /**
     * @return bool
     */
    public function isToMany()
    {
        return $this->isOneToMany() || $this->isManyToMany();
    }

    /**
     * @return bool
     */
    public function isToOne()
    {
        return $this->isOneToOne() || $this->isManyToOne();
    }

    /**
     * @return string
     */
    public function getTargetEntityName()
    {
        return $this->targetEntityName;
    }

    /**
     * @return string
     */
    public function getTargetRelationName()
    {
        return $this->targetRelationName;
    }

    /**
     * @return bool|null
     */
    public function isInversed()
    {
        return $this->isInversed;
    }

    /**
     * @return bool
     */
    public function isUni()
    {
        return $this->isInversed === null;
    }

    /**
     * @return string
     */
    public function getTargetEntityClass()
    {
        return $this->targetEntityClass;
    }

    /**
     * @return ObjectRepository
     */
    public function getTargetRepository()
    {
        return Mif::getDoctrine()->getRepository($this->getTargetEntityClass());
    }

    /**
     * @return string
     */
    public function getGetterName()
    {
        return EntityConstants::ACTION_GET . ucfirst($this->getName());
    }

    /**
     * @return string
     */
    public function getSetterName()
    {
        if ($this->isToMany()) {
            return EntityConstants::ACTION_ADD . ucfirst($this->getName());
        }

        if ($this->isToOne()) {
            return EntityConstants::ACTION_SET . ucfirst($this->getName());
        }

        return '';
    }

    /**
     * @return string
     */
    public function getRemoverName()
    {
        if ($this->isToMany()) {
            return EntityConstants::ACTION_REMOVE . ucfirst($this->getName());
        }

        return '';
    }

    /**
     * @return string
     */
    public function getRelatedSetterName()
    {
        if ($this->isOneToMany()) {
            return EntityConstants::ACTION_SET . ucfirst($this->getTargetRelationName());
        }

        if ($this->isManyToMany()) {
            return EntityConstants::ACTION_ADD . ucfirst($this->getTargetRelationName());
        }

        if ($this->isOneToOne()) {
            return EntityConstants::ACTION_SET . ucfirst($this->getTargetRelationName());
        }

        if ($this->isManyToOne()) {
            return EntityConstants::ACTION_ADD . ucfirst($this->getTargetRelationName());
        }

        return '';
    }

    /**
     * @param BaseEntity $entity
     * @return array
     */
    public function extractAsArray($entity)
    {
        if ($this->isToMany()) {
            return $entity->getAttribute($this->name);
        }

        if (!$entity->getAttribute($this->name)) {
            return [];
        }

        return [$entity->getAttribute($this->name)];
    }

    /**
     * @return array
     */
    public function getAllowedActions()
    {
        if ($this->isToOne()) {
            return [
                EntityConstants::ACTION_GET,
                EntityConstants::ACTION_SET,
            ];
        }

        return [
            EntityConstants::ACTION_GET,
            EntityConstants::ACTION_ADD,
            EntityConstants::ACTION_REMOVE,
        ];
    }

    /**
     * @param array $data
     */
    private function init($data)
    {
        if (isset($data['inversedBy'])) {
            $this->isInversed = true;
            $this->targetRelationName = $data['inversedBy'];
        } elseif (isset($data['mappedBy'])) {
            $this->isInversed = false;
            $this->targetRelationName = $data['mappedBy'];
        }

        $this->targetEntityClass = $data['targetEntity'];
        $this->targetEntityName = (explode(
            NamespaceHelper::getEntityDefaultNamespace() . '\\',
            $this->targetEntityClass
        ))[1];
    }
}
