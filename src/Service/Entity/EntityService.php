<?php

namespace App\Service\Entity;

use App\Constants;
use App\EntitySupport\Pagination\EntitiesPage;
use App\EntitySupport\Pagination\EntitiesPageLoader;
use App\Exception\BadRequestException;
use App\Exception\EntityNotFoundException;
use App\Mif;
use App\Repository\BaseRepository;
use App\Service\Service;
use App\EntitySupport\Common\BaseEntity;
use App\EntitySupport\Common\EntitySchema;
use Exception;
use Psr\Container\ContainerInterface;
use App\Request;

/**
 * Class EntityService
 * @package App\Service\Entity
 */
abstract class EntityService extends Service
{
    /** @var BaseEntity */
    protected $entityClassName;

    /**
     * EntityService constructor.
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container);
        $this->entityClassName = $this->getEntityClassName();
    }

    /**
     * @return EntitySchema
     */
    public function getSchema()
    {
        return $this->entityClassName::getSchema();
    }

    /**
     * @return BaseRepository
     */
    public function getRepository()
    {
        /** @var BaseRepository $result */
        $result = $this->getDoctrine()->getRepository($this->entityClassName);
        return $result;
    }

    /**
     * @param array $properties
     * @return BaseEntity
     */
    public function getNewEntityInstance($properties = [])
    {
        /** @var BaseEntity $instance */
        $instance = new $this->entityClassName();
        $instance->setProperties($properties);
        return $instance;
    }

    /**
     * @param int $id
     * @return BaseEntity|null
     */
    public function getEntity($id)
    {
        /** @var BaseEntity $entity */
        $entity = $this->getRepository()->find($id);
        return $entity;
    }

    /**
     * @return BaseEntity[]
     */
    public function getEntities()
    {
        /** @var BaseEntity[] $entities */
        $entities = $this->getRepository()->findAll();
        return $entities;
    }

    /**
     * @param array $filters
     * @param array $options
     * @return EntitiesPage
     */
    public function getEntitiesPage($filters = [], $options = [])
    {
        $loader = new EntitiesPageLoader($this);
        $loader->setFilters($filters);
        $loader->setOptions($options);
        $page = $loader->loadPage();
        return $page;
    }

    /**
     * @param array $properties
     * @return BaseEntity
     */
    public function createEntity($properties)
    {
        $entity = $this->getNewEntityInstance($properties);
        $entity->save();

        return $entity;
    }

    /**
     * @param BaseEntity $entity
     * @param array $properties
     */
    public function updateEntity($entity, $properties)
    {
        $schema = $this->getSchema();
        foreach ($properties as $propertyName => $propertyData) {
            if ($schema->hasAttribute($propertyName)) {
                $entity->setAttribute($propertyName, $propertyData);
                continue;
            }

            if ($schema->hasRelation($propertyName)) {
                $entity->setRelation($propertyName, $propertyData);
            }
        }

        $entity->save();
    }

    /**
     * @param BaseEntity $entity
     */
    public function removeEntity($entity)
    {
        Mif::getPersistHolder()->hold();

        try {
            $schema = $this->getSchema();
            $relations = $schema->getRelations();
            foreach ($relations as $relation) {
                if ($relation->isOneToOne()) {
                    $relEntity = $entity->getRelatedEntity($relation->getName());
                    if ($relEntity) {
                        $relEntity->remove();
                    }
                } elseif ($relation->isOneToMany()) {
                    //TODO проверять на наличие привязанных сущностей, если есть выбрасывать исключение. Тогда try-catch будет не нужен
                } elseif ($relation->isManyToMany()) {
                    $entity->setRelation($relation->getName(), []);
                } elseif ($relation->isManyToOne()) {
                    $entity->setRelation($relation->getName(), null);
                }
            }
            $entity->remove();

            Mif::getPersistHolder()->commit();
        } catch (Exception $exception) {
            Mif::getPersistHolder()->drop();
            throw new BadRequestException('Не удается удалить. Сначала отвяжите связанные сущности.');
        }
    }

    /**
     * @param Request $request
     * @param array $aliases
     * @return EntitiesPage
     */
    public function getEntitiesPageFromRequest($request, $aliases = [])
    {
        $filters = $this->extractAttributesFromRequest($request, $aliases);
        $options = [
            'page' => $request->get('page', 0),
            'limit' => $request->get('pageSize', Constants::PAGE_LIMIT),
            'sortType' => $request->get('sortType'),
            'sortOrder' => $request->get('sortOrder'),
            'query' => $request->get('query'),

            //TODO временный параметр для интергации со старым бэком
            'fullInfo' => $request->get('fullInfo', false),
        ];

        return $this->getEntitiesPage($filters, $options);
    }

    /**
     * @param Request $request
     * @param array $aliases
     * @return BaseEntity
     */
    public function createEntityFromRequest($request, $aliases = [])
    {
        $properties = $this->extractPropertiesFromRequest($request, $aliases);
        $this->preprocessProperties($properties);
        return $this->createEntity($properties);
    }

    /**
     * @param Request $request
     * @param array $aliases
     * @return BaseEntity
     */
    public function updateEntityFromRequest($request, $aliases = [])
    {
        $entity = $this->getEntity($request->get('id'));
        if (!$entity) {
            throw new EntityNotFoundException('Объект не найден');
        }

        $properties = $this->extractPropertiesFromRequest($request, $aliases);
        $this->preprocessProperties($properties);
        $this->updateEntity($entity, $properties);
        return $entity;
    }

    /**
     * @param Request $request
     */
    public function deleteEntityFromRequest($request)
    {
        $entity = $this->getEntity($request->get('id'));
        if (!$entity) {
            throw new EntityNotFoundException('Объект не найден');
        }

        $this->removeEntity($entity);
    }

    /**
     * @param $alias
     * @param $joins
     * @param $conditions
     * @param $sortBy
     * @param $page
     * @param $limit
     * @return mixed
     */
    public function getEntityByAttributesAndSort($alias, $joins, $conditions, $sortBy, $page, $limit)
    {
        return $this->getRepository()->getEntityByAttributesAndSort(
            $alias,
            $joins,
            $conditions,
            $sortBy,
            $page,
            $limit
        );
    }

    /**
     * @param Request $request
     * @param array $aliases
     * @return array
     */
    public function extractAttributesFromRequest($request, $aliases = [])
    {
        $schema = $this->getSchema();
        $attributeNames = $schema->getAttributeNames();
        $aliases = array_flip($aliases);

        $attributes = [];
        foreach ($attributeNames as $name) {
            $nameInRequest = array_key_exists($name, $aliases) ? $aliases[$name] : $name;
            if ($request->has($nameInRequest)) {
                $attributes[$name] = $request->get($nameInRequest);
            }
        }

        return $attributes;
    }

    /**
     * @param Request $request
     * @param array $aliases
     * @return array
     */
    public function extractRelationsFromRequest($request, $aliases = [])
    {
        $schema = $this->getSchema();
        $relationNames = $schema->getRelationNames();
        $aliases = array_flip($aliases);

        $relations = [];
        foreach ($relationNames as $name) {
            $nameInRequest = array_key_exists($name, $aliases) ? $aliases[$name] : $name;
            if ($request->has($nameInRequest)) {
                $relations[$name] = $request->get($nameInRequest);
            }
        }

        return $relations;
    }

    /**
     * @param Request $request
     * @param array $aliases
     * @return array
     */
    public function extractPropertiesFromRequest($request, $aliases = [])
    {
        return array_merge(
            $this->extractAttributesFromRequest($request, $aliases),
            $this->extractRelationsFromRequest($request, $aliases)
        );
    }

    /**
     * Метод для переопределения в потомках
     * Провести необходимые преобразования с параметрами запроса для работы методов:
     * - createEntityFromRequest()
     * - updateEntityFromRequest()
     *
     * @param array $properties
     */
    protected function preprocessProperties(&$properties)
    {
        // pass
    }

    /**
     * @return string
     */
    abstract protected function getEntityClassName();
}
