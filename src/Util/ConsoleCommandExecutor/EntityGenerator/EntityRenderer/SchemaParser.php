<?php

namespace App\Util\ConsoleCommandExecutor\EntityGenerator\EntityRenderer;

use App\EntitySupport\Behavior\ChangeTimeSavingBehavior;
use App\EntitySupport\Common\EntityConstants;
use App\EntitySupport\Common\EntitySchema;
use App\Enum\OrmRelationTypeEnum;
use App\Helper\NamespaceHelper;
use App\Mif;
use App\Util\Common\DefinitionSyntaxHelper;
use App\Util\Common\MessageKeeperTrait;
use App\Util\ConsoleCommandExecutor\EntityGenerator\EntityGeneratorHelper;
use ReflectionException;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Yaml;

/**
 * Class SchemaParser
 * @package App\Util\ConsoleCommandExecutor\EntityGenerator\EntityRenderer
 */
class SchemaParser
{
    use MessageKeeperTrait;

    /** @var string */
    private $entityName;

    /** @var Schema */
    private $schema;

    /** @var array */
    private $forUse = [];

    /** @var array */
    private $ormExtensions = [];

    /** @var array */
    private $interfaces = [];

    /** @var array */
    private $forTraits = [];

    /**
     * SchemaParser constructor.
     * @param string $entityName
     */
    public function __construct($entityName)
    {
        $this->entityName = $entityName;
    }

    /**
     * @return Schema
     */
    public function getSchema()
    {
        return $this->schema;
    }

    public function run()
    {
        $spec = $this->getSpecification();
        if (!$spec) {
            return;
        }

        $this->schema = new Schema([
            'name' => $spec['name'],
            'table' => $spec['table'],
            'attributes' => $spec['attributes'],
            'extraAttributes' => $spec['extraAttributes'],
            'relations' => $spec['relations'],
            'forUse' => $this->forUse,
            'forTraits' => $this->forTraits,
            'ormExtensions' => $this->ormExtensions,
            'interfaces' => $this->interfaces,
        ]);
    }

    /**
     * @param string $entityName
     * @return array|false
     */
    private function loadSpecification($entityName = null)
    {
        if ($entityName === null) {
            $entityName = $this->entityName;
        }

        $specFileName = Mif::getProjectDir() . EntityGeneratorHelper::getYamlEntityPath($entityName);
        if (!file_exists($specFileName)) {
            $this->addErrorRow("Trying to open file '$specFileName' by it doesn't exist");
            return false;
        }
        $text = file_get_contents($specFileName);
        try {
            $result = Yaml::parse($text);
            return $result;
        } catch (ParseException $exception) {
            $this->addErrorRow("File '{$specFileName}' parsing error: " . $exception->getMessage());
            return false;
        }
    }

    /**
     * @param array $spec
     * @return bool
     */
    private function validateSpecification($spec)
    {
        if (!isset($spec['name']) || !isset($spec['attributes'])) {
            $this->addErrorRow('Error. Specification must have fields "name" and "attributes"');
            return false;
        }

        if ($spec['name'] != $this->entityName) {
            $this->addErrorRow('The entity name in specification must be the same as the entity file name');
            return false;
        }

        $availableKeys = ['name', 'table', 'behaviors', 'interfaces', 'attributes', 'relations', 'defaults'];
        foreach ($spec as $key => $item) {
            if (array_search($key, $availableKeys) === false) {
                $this->addErrorRow("Wrong specification key: '$key'");
                return false;
            }
        }

        $keyWords = [
            'readOnlyAttributesList',
            'schema',
            'service',
            'attribute',
            'attributes',
            'property',
            'properties',
            'relation',
            'relations',
            'relatedEntity',
            'className',
        ];
        foreach ($spec['attributes'] as $name => $attribute) {
            if (array_search($name, $keyWords) !== false) {
                $this->addErrorRow("Attribute name '$name' is not allowed. This is key word.");
                return false;
            }
        }

        if (array_key_exists('relations', $spec)) {
            foreach ($spec['relations'] as $name => $relation) {
                if (array_search($name, $keyWords) !== false) {
                    $this->addErrorRow("Relation name '$name' is not allowed. This is key word.");
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * @return array|false
     */
    private function getSpecification()
    {
        $this->addTextRow('Specification parsing have started...');
        $spec = $this->loadSpecification();
        if (!$spec) {
            return false;
        }

        if (!$this->validateSpecification($spec)) {
            return false;
        }

        $this->parseInterfaces($spec['interfaces'] ?? []);

        $schemaMap = [
            'name' => $spec['name'],
            'table' => $spec['table'] ?? '',
            'behaviors' => $this->defineBehaviors($spec),
        ];

        $attributes = $this->defineAttributes($spec);
        if ($attributes === false) {
            return false;
        }

        $relations = $this->defineRelations($spec);
        if ($relations === false) {
            return false;
        }

        $schemaMap['attributes'] = $attributes;
        $schemaMap['relations'] = $relations;
        $schemaMap['extraAttributes'] = $this->defineExtraAttributes($schemaMap);
        return $schemaMap;
    }

    /**
     * @param array $interfaces
     */
    private function parseInterfaces($interfaces)
    {
        foreach ($interfaces as $interface) {
            $fullName = NamespaceHelper::defineClassName($interface, [
                NamespaceHelper::getEntityInterfaceDefaultNamespace(),
                NamespaceHelper::getInterfaceDefaultNamespace()
            ]);
            $arr = explode('\\', $fullName);
            $name = array_pop($arr);
            $this->interfaces[] = $name;
            $this->forUse[] = $fullName;
        }
    }

    /**
     * @param array $spec
     * @return array
     */
    private function defineBehaviors($spec)
    {
        $behaviors = $spec['behaviors'] ?? [];
        $result = [];
        foreach ($behaviors as $behavior) {
            $className = NamespaceHelper::defineClassName($behavior, NamespaceHelper::getBehaviorDefaultNamespace());
            if (trait_exists($className)) {
                $result[] = [
                    'name' => $behavior,
                    'trait' => $className,
                ];
                $this->forUse[] = $className;
                $this->forTraits[] = $behavior;

                if ($className == ChangeTimeSavingBehavior::class) {
                    if (array_search('@ORM\HasLifecycleCallbacks', $this->ormExtensions) === false) {
                        $this->ormExtensions[] = '@ORM\HasLifecycleCallbacks';
                    }
                }
            }
        }
        return $result;
    }

    /**
     * @param array $spec
     * @return array|false
     */
    private function defineAttributes($spec)
    {
        $defaults = $spec['defaults'] ?? [];
        $attributes = $spec['attributes'] ?? [];
        $result = [];
        foreach ($attributes as $attributeName => $attribute) {
            $attributeData = $this->parseAttribute($attributeName, $attribute, $defaults);
            if (!$attributeData) {
                return false;
            }

            $result[$attributeName] = $attributeData;
        }
        return $result;
    }

    /**
     * @param array $spec
     * @return array|false
     */
    private function defineRelations($spec)
    {
        $relations = $spec['relations'] ?? [];
        $result = [];
        foreach ($relations as $relationName => $relation) {
            $relationData = $this->parseRelation($relationName, $relation);
            if (!$relationData) {
                return false;
            }

            $result[$relationName] = $relationData;
        }
        return $result;
    }

    /**
     * @param array $map
     * @return array
     */
    private function defineExtraAttributes($map)
    {
        $result = [];
        foreach ($map['behaviors'] as $behavior) {
            $trait = $behavior['trait'];
            try {
                $traitMap = EntitySchema::parseClass($trait);
                $attributes = $traitMap['attributes'] ?? [];
                foreach ($attributes as $name => $attribute) {
                    if (!array_key_exists(EntityConstants::ATTRIBUTE_ORM_DATA, $attribute)) {
                        continue;
                    }
                    $result[$name] = [
                        'readonly' => false,
                        'orm' => $attribute[EntityConstants::ATTRIBUTE_ORM_DATA],
                        'app' => [],
                    ];

                    $type = $attribute[EntityConstants::ATTRIBUTE_ORM_DATA]['type'];
                    if ($type == 'datetime') {
                        if (array_search('DateTime', $this->forUse) === false) {
                            $this->forUse[] = 'DateTime';
                        }
                    }

                }
            } catch (ReflectionException $exception) {
                continue;
            }
        }
        return $result;
    }

    /**
     * @param string $attributeName
     * @param string|array $attribute
     * @param array $defaults
     * @return array|false
     */
    private function parseAttribute($attributeName, $attribute, $defaults)
    {
        $defaults['nullable'] = $defaults['nullable'] ?? true;
        $defaults['readonly'] = $defaults['readonly'] ?? false;
        $defaults['required'] = $defaults['required'] ?? false;
        $definitionArray = DefinitionSyntaxHelper::parseAttribute($attribute, $defaults);
        if (!$definitionArray) {
            $this->addErrorRow("Wrong attribute definition for '{$attributeName}'");
            return false;
        }

        $result = [
            'readonly' => $definitionArray['readonly'],
            'orm' => [],
            'app' => [],
        ];

        $type = $definitionArray['type'];
        if (preg_match('/^(.+?)\((.+?)\)$/', $type, $matches)) {
            $type = $matches[1];
            $length = (int)$matches[2];
        } else {
            $length = null;
        }

        if ($type == 'string' && $length === null) {
            $length = 255;
        }

        if ($type == 'datetime') {
            if (array_search('DateTime', $this->forUse) === false) {
                $this->forUse[] = 'DateTime';
            }
        }

        $result['orm']['type'] = $type;
        if ($length) {
            $result['orm']['length'] = $length;
        }
        $result['orm']['nullable'] = $definitionArray['nullable'];

        if (array_key_exists('default', $definitionArray)) {
            $result['orm']['default'] = $definitionArray['default'];
        }

        if (array_key_exists('constraints', $definitionArray)) {
            foreach ($definitionArray['constraints'] as $constraint) {
                if (preg_match('/^([A-Z]\w*?\b)(?:\((.+?)\)|\(\))?$/', $constraint, $matches)) {
                    if (!empty($matches[0])) {
                        $constraint = $matches[1];
                        $params = $matches[2] ?? '';
                        $option = EntityConstants::ATTRIBUTE_APP_CONSTRAINT . ":$constraint($params)";
                        $result['app'][] = $option;
                    }
                }
            }
        }

        if (array_key_exists('inForm', $definitionArray)) {
            $result['app'][] = EntityConstants::ATTRIBUTE_APP_FIELD . "(inForm=\"{$definitionArray['inForm']}\")";
        }

        return $result;
    }

    /**
     * @param string $relationName
     * @param string|array $relation
     * @return array|false
     */
    private function parseRelation($relationName, $relation)
    {
        /**
         * @var string $type
         * @var bool $fkHost
         * @var string $relEntity
         * @var string|null $relEntityAttribute
         * @var bool $orphanRemoval
         */
        extract($this->parseRelationDefinition($relation));

        if (!OrmRelationTypeEnum::validateValue($type)) {
            $this->addErrorRow("Wrong relation type for '{$relationName}'");
            return false;
        }

        if (!$relEntityAttribute) {
            if ($type == OrmRelationTypeEnum::ONE_TO_MANY || $type == OrmRelationTypeEnum::MANY_TO_MANY) {
                $this->addErrorRow("Relation '{$relationName}' must have relative entity attribute");
                return false;
            }

            $uni = true;
        } else {
            $uni = false;
        }

        $relNeedToBeFkHost = null;
        if ($type == OrmRelationTypeEnum::ONE_TO_ONE) {
            $relNeedToBeFkHost = !$fkHost;
        }
        if (!$uni
            && !$this->checkEntityRelation(
                $relationName,
                $type,
                $relEntity,
                $relEntityAttribute,
                $relNeedToBeFkHost
            )
        ) {
            return false;
        }

        if ($type == OrmRelationTypeEnum::MANY_TO_MANY || $type == OrmRelationTypeEnum::ONE_TO_MANY) {
            if (array_search('Doctrine\Common\Collections\Collection', $this->forUse) === false) {
                $this->forUse[] = 'Doctrine\Common\Collections\Collection';
            }
            if (array_search('Doctrine\Common\Collections\ArrayCollection', $this->forUse) === false) {
                $this->forUse[] = 'Doctrine\Common\Collections\ArrayCollection';
            }
        }

        $relName = EntityGeneratorHelper::getEntitySimpleName($relEntity);
        $relAlias = $relName;
        $relNamespace = EntityGeneratorHelper::getEntityNamespace($relEntity);
        $selfNamespace = EntityGeneratorHelper::getEntityNamespace($this->entityName);
        $relClassName = $relNamespace == ''
            ? NamespaceHelper::getEntityDefaultNamespace() . '\\' . $relName
            : NamespaceHelper::getEntityDefaultNamespace() . '\\' . $relNamespace . '\\' . $relName;
        if ($selfNamespace != $relNamespace) {
            if ($relNamespace == '') {
                $this->forUse[] = $relClassName;
            } else {
                $relAlias = str_replace('\\', '', $relNamespace) . $relName;
                $this->forUse[] = [
                    'className' => $relClassName,
                    'as' => $relAlias,
                ];
            }
        }

        return [
            'type' => $type,
            'uni' => $uni,
            'fkHost' => $fkHost,
            'relEntity' => $relEntity,
            'relAlias' => $relAlias,
            'relClassName' => $relClassName,
            'relAttribute' => $relEntityAttribute,
            'orphanRemoval' => $orphanRemoval,
        ];
    }

    /**
     * @param string $basicName
     * @param string $type
     * @param string $entity
     * @param $relationName
     * @param bool|null $fkHost
     * @return bool
     */
    private function checkEntityRelation($basicName, $type, $entity, $relationName, $fkHost)
    {
        $errString = "Error on relation '$basicName'. ";

        $spec = $this->loadSpecification($entity);
        if (!$spec) {
            $this->addErrorRow(
                $errString
                . "Checking of '$entity::$relationName' existence has failed. Specification can't be loaded"
            );
            return false;
        }

        $relation = $spec['relations'][$relationName] ?? null;
        if (!$relation) {
            $this->addErrorRow(
                $errString . "Relation is connected to '$entity::$relationName' which has to be defined"
            );
            return false;
        }

        $relType = OrmRelationTypeEnum::getContrType($type);
        if (!$relType) {
            $this->addErrorRow($errString . "Can't define type for '$entity::$relationName'");
            return false;
        }

        $definition = $this->parseRelationDefinition($relation);
        if ($definition['type'] != $relType) {
            $this->addErrorRow(
                $errString
                . "For relation type '$type' is expected contr-type '$relType', type '{$definition['type']}' given"
            );
            return false;
        }

        $basicEntity = $this->entityName;
        if ($definition['relEntity'] != $basicEntity) {
            $this->addErrorRow(
                $errString
                . "Wrong entity definition in '$entity::$relationName'. "
                . "Is expected '$basicEntity', '{$definition['relEntity']}' given"
            );
            return false;
        }

        if ($definition['relEntityAttribute'] != $basicName) {
            $this->addErrorRow(
                $errString
                . "Wrong relation definition in '$entity::$relationName'. "
                . "Is expected '$basicName', '{$definition['relEntityAttribute']}' given"
            );
            return false;
        }

        if ($fkHost !== null) {
            if ($fkHost && !$definition['fkHost']) {
                $this->addErrorRow(
                    $errString
                    . "Relations '$basicEntity::$basicName' and  '$entity::$relationName' "
                    . "are without FK-anchor. You have to choose one"
                );
                return false;
            } elseif (!$fkHost && $definition['fkHost']) {
                $this->addErrorRow(
                    $errString
                    . "Relations '$basicEntity::$basicName' and  '$entity::$relationName' "
                    . "are both have FK-anchor. You have to choose only one"
                );
            }
        }

        return true;
    }

    /**
     * @param string $definition
     * @return array
     */
    private function parseRelationDefinition($definition)
    {
        $definitionArray = preg_split('/ +/',$definition);

        $type = $definitionArray[0] ?? '';
        $fkHost = false;
        if (preg_match('/fk\)?$/', $type)) {
            $fkHost = true;
            $type = preg_replace('/fk\)?$/', '', $type);
        }
        $type = trim($type, ')(');
        switch ($type) {
            case '--':
                $type = OrmRelationTypeEnum::ONE_TO_ONE;
                break;
            case '-<':
                $type = OrmRelationTypeEnum::ONE_TO_MANY;
                break;
            case '>-':
                $type = OrmRelationTypeEnum::MANY_TO_ONE;
                break;
            case '><':
                $type = OrmRelationTypeEnum::MANY_TO_MANY;
                break;
        }

        $relEntityArray = explode('.', ($definitionArray[1] ?? ''));
        $relEntity = $relEntityArray[0];
        $relEntityAttribute = $relEntityArray[1] ?? null;

        $orphanRemoval = (($definitionArray[2] ?? '') == 'orphanRemoval');

        return [
            'type' => $type,
            'fkHost' => $fkHost,
            'relEntity' => $relEntity,
            'relEntityAttribute' => $relEntityAttribute,
            'orphanRemoval' => $orphanRemoval,
        ];
    }
}
