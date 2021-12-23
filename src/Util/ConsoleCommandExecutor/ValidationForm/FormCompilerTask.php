<?php

namespace App\Util\ConsoleCommandExecutor\ValidationForm;

use App\EntitySupport\Common\BaseEntity;
use App\Helper\NamespaceHelper;
use App\Interfaces\ConstraintInterface;
use App\Mif;
use App\Util\Common\DefinitionSyntaxHelper;
use App\Util\ConsoleCommandExecutor\Swagger\SwaggerConstants;
use App\Util\Task\Task;
use App\Util\ConsoleCommandExecutor\EntityGenerator\EntityGeneratorHelper;
use App\Validation\ConstraintCore;
use App\Validation\Constraint\Enum;
use App\Validation\ValidationFormMacros\AbstractMacros;
use Symfony\Component\Validator\Constraints\GreaterThanOrEqual;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\LessThanOrEqual;
use Symfony\Component\Yaml\Yaml;

/**
 * Class FormCompilerTask
 * @package App\Util\ConsoleCommandExecutor\ValidationForm
 */
class FormCompilerTask extends Task
{
    const NONE_PROCESSING = 0;
    const INPUT_FORM_PROCESSING = 1;
    const OUTPUT_FORM_PROCESSING = 2;

    /** @var string */
    private $formsGroupName;

    /** @var string */
    private $sourceFilePath;

    /** @var array */
    private $metaData;

    /** @var array|string */
    private $summary;

    /** @var array|string */
    private $description;

    /** @var array */
    private $inputFormMap;

    /** @var array */
    private $outputFormMap;

    /** @var array */
    private $mapping;

    /** @var array */
    private $mapData;

    /** @var string */
    private $currentMapKey;

    /** @var array */
    private $dependencies;

    /** @var int */
    private $processingType;

    /**
     * FormCompilerTask constructor.
     * @param array $config
     */
    public function __construct($config)
    {
        parent::__construct($config);
        $this->processingType = self::NONE_PROCESSING;

        $route = $this->metaData['Path'];
        $method = $this->metaData['Method'];
        $this->addTitle("* Compiling for route <fg=magenta>{$route}</>, API method <fg=magenta>{$method}</>");
    }

    /**
     * @param array $data
     */
    public function setData($data)
    {
        $this->formsGroupName = $data['group'];
        $this->sourceFilePath = $data['sourceFile'];
        $map = $data['data'];
        $this->metaData = $map['MetaData'];
        $this->summary = $map['Summary'] ?? '';
        $this->description = $map['Description'] ?? '';
        $this->inputFormMap = $map['InputForm'];
        $this->outputFormMap = $map['OutputForm'];
        $this->mapping = $map['Mapping'] ?? [];
    }

    protected function run()
    {
        $this->dependencies = [];

        /** @var TaskContext $context */
        $context = $this->getContext();
        $key = $context->getKeyForFormsMap($this->metaData);

        if (empty($this->mapping)) {
            $this->runIteration();
            $mapData = $this->mapData;
        } else {
            $this->mapData = [];
            $mapData = [
                'mapping' => [
                    'by' => $this->mapping['by'],
                    'map' => [],
                ],
            ];

            foreach ($this->mapping['map'] as $mapKey => $mapValue) {
                $this->currentMapKey = $mapKey;
                $this->runIteration();
            }

            $mapData['mapping']['map'] = $this->mapData;
        }
        $mapData['dependencies'] = $this->dependencies;
        $context->addToFormsMap($key, $mapData);
    }

    private function runIteration()
    {
        $summary = $this->processSummary();
        if ($summary === false) {
            return;
        }
        $description = $this->processDescription();
        if ($description === false) {
            return;
        }
        $inputForm = $this->processInputForm();
        if ($inputForm === false) {
            return;
        }
        $outputForm = $this->processOutputForm();
        if ($outputForm === false) {
            return;
        }

        $swaggerFilePath = $this->getSwaggerFormFilePath();
        $swaggerInputForm = $this->processInputFormForSwagger($inputForm);
        $swaggerOutputForm = $this->processOutputFormForSwagger($outputForm);
        file_put_contents($swaggerFilePath, json_encode([
            'summary' => $summary,
            'description' => $description,
            'inputForm' => $swaggerInputForm,
            'outputForm' => $swaggerOutputForm,
        ], JSON_PRETTY_PRINT));
        $this->addTextRow("File '$swaggerFilePath' has been updated");

        $valFormFilePath = $this->getValidationFormFilePath();
        $validationInputForm = $this->processInputFormForValidation($inputForm);
        //TODO $validationOutputForm = $this->processOutputFormForValidation($outputForm);
        file_put_contents($valFormFilePath, json_encode([
            'inputForm' => $validationInputForm,
            'outputForm' => $outputForm,
        ], JSON_PRETTY_PRINT));
        $this->addTextRow("File '$valFormFilePath' has been updated");

        $this->setMapData(
            (explode(Mif::getProjectDir(), $swaggerFilePath))[1],
            (explode(Mif::getProjectDir(), $valFormFilePath))[1]
        );
    }

    /**
     * @param array $inputForm
     * @return array
     */
    private function processInputFormForSwagger($inputForm)
    {
        $result = [];

        if ($this->metaData['Authentication'] == 'required') {
            $result['security'] = ['ApiKeyAuth' => []];
            $result['parameters'] = [
                [
                    'in' => 'header',
                    'name' => 'Access-Token',
                    'schema' => ['type' => 'string'],
                    'required' => true,
                    'description' => [
                        'en' => 'Access token required for authentication',
                        'ru' => 'Обязательный токен доступа для аутентификации',
                    ],
                ],
            ];
        }

        if (array_key_exists('parameters', $inputForm)) {
            $parameters = [];
            foreach ($inputForm['parameters'] as $parameterData) {
                $flags = $parameterData['flags'] ?? [];
                if (in_array('-docIgnore', $flags)) {
                    continue;
                }

                $parameter = [
                    'name' => $parameterData['name'],
                    'in' => $parameterData['in'],
                ];

                if (array_key_exists('required', $parameterData)) {
                    $parameter['required'] = $parameterData['required'];
                }

                $descriptionArray = ['en' => [], 'ru' => []];
                if ($parameterData['type'] == 'array') {
                    $items = $parameterData['items'] ?? [
                        '$type' => 'string',
                    ];

                    if (array_key_exists('constraints', $items)) {
                        $constraintComments = $this->getConstraintComment($items['constraints'], [
                            'ru' => 'На элементы массива наложены ограничения:',
                            'en' => 'Array items have constraints:',
                        ]);
                        $constraints = $this->processConstraintsForSwagger($items['constraints']);
                        unset($items['constraints']);
                        if ($constraints) {
                            $items = array_merge($items, $constraints);

                            $descriptionArray['en'][] = $constraintComments['en'];
                            $descriptionArray['ru'][] = $constraintComments['ru'];
                        }
                    }

                    $type = [
                        'type' => 'array',
                        'items' => $items,
                    ];
                } else {
                    $type = DefinitionSyntaxHelper::translateTypeForForm($parameterData['type']);
                }
                if (isset($type['format'])) {
                    $parameter['format'] = $type['format'];
                    unset($type['format']);
                }
                $parameter['schema'] = $type;

                if (array_key_exists('nullable', $parameterData)) {
                    $parameter['schema']['nullable'] = $parameterData['nullable'];
                }

                if (array_key_exists('default', $parameterData)) {
                    $parameter['schema']['default'] = $parameterData['default'];
                }

                if (array_key_exists('constraints', $parameterData)) {
                    $constraintComments = $this->getConstraintComment($parameterData['constraints'], [
                        'ru' => 'На параметр наложены ограничения:',
                        'en' => 'Parameter has constraints:',
                    ]);
                    $constraints = $this->processConstraintsForSwagger($parameterData['constraints']);
                    if ($constraints) {
                        $parameter['schema'] = array_merge($parameter['schema'], $constraints);

                        $descriptionArray['en'][] = $constraintComments['en'];
                        $descriptionArray['ru'][] = $constraintComments['ru'];
                    }
                }

                if (array_key_exists('description', $parameterData)) {
                    $description = $parameterData['description'];
                    $descriptionArray['en'][] = is_string($description) ? $description : ($description['en'] ?? '');
                    $descriptionArray['ru'][] = is_string($description) ? $description : ($description['ru'] ?? '');
                }
                if (!empty($descriptionArray['en'])) {
                    $parameter['description'] = [
                        'en' => implode(PHP_EOL, array_reverse($descriptionArray['en'])),
                        'ru' => implode(PHP_EOL, array_reverse($descriptionArray['ru'])),
                    ];
                }

                $parameters[] = $parameter;
            }

            if (!array_key_exists('parameters', $result)) {
                $result['parameters'] = [];
            }
            $result['parameters'] = array_merge($result['parameters'], $parameters);
        }

        if (array_key_exists('body', $inputForm)) {
            $result['body'] = $this->processFormObjectForSwagger($inputForm['body']);
        }

        return $result;
    }

    /**
     * @param array $outputForm
     * @return array
     */
    private function processOutputFormForSwagger($outputForm)
    {
        $result = [];

        $re = function(&$arr) use (&$re) {
            foreach ($arr as $key => &$value) {
                if ($key == 'required') {
                    unset($arr[$key]);
                    continue;
                }

                if ($key == 'enum') {
                    continue;
                }

                if ($key == 'constraints') {
                    $constraints = $this->processConstraintsForSwagger($value);
                    unset($arr['constraints']);
                    $arr = array_merge($arr, $constraints);
                    continue;
                }

                if (is_array($value)) {
                    $re($value);
                }
            }
            unset($value);
        };
        $re($outputForm);

        $result['data'] = $outputForm;

        $errors = [400];
        if ($this->metaData['Authentication'] == 'required') {
            $errors[] = 401;
            $errors[] = 403;
        }
        $errors[] = 404;
        $result['errors'] = $errors;

        return $result;
    }

    /**
     * @param array $objectSchema
     * @return array
     */
    private function processFormObjectForSwagger($objectSchema)
    {
        $result = ['type' => 'object'];
        if (array_key_exists('description', $objectSchema)) {
            $result['description'] = $objectSchema['description'];
        }
        // $result['properties'] = [];
        $properties = [];
        foreach ($objectSchema['properties'] ?? [] as $name => $propertyData) {
            if ($propertyData['type'] == 'object') {
                $result['properties'][$name] = $this->processFormObjectForSwagger($propertyData);
            } else {
                $property = DefinitionSyntaxHelper::translateTypeForForm($propertyData['type']);
                unset($propertyData['type']);
                $constraints = null;
                if (isset($propertyData['constraints'])) {
                    $constraints = $propertyData['constraints'];
                    unset($propertyData['constraints']);
                }

                $property = array_merge($property, $propertyData);
                $descriptionArray = ['en' => [], 'ru' => []];
                if ($constraints) {
                    $constraintComments = $this->getConstraintComment($constraints, [
                        'ru' => 'На параметр наложены ограничения:',
                        'en' => 'Parameter has constraints:',
                    ]);

                    $constraints = $this->processConstraintsForSwagger($constraints);
                    $property = array_merge($property, $constraints);
                    $descriptionArray['en'][] = $constraintComments['en'];
                    $descriptionArray['ru'][] = $constraintComments['ru'];
                }

                if (array_key_exists('description', $property)) {
                    $description = $property['description'];
                    $descriptionArray['en'][] = is_string($description) ? $description : ($description['en'] ?? '');
                    $descriptionArray['ru'][] = is_string($description) ? $description : ($description['ru'] ?? '');
                }
                if (!empty($descriptionArray['en'])) {
                    $property['description'] = [
                        'en' => implode(PHP_EOL, array_reverse($descriptionArray['en'])),
                        'ru' => implode(PHP_EOL, array_reverse($descriptionArray['ru'])),
                    ];
                }

                $result['properties'][$name] = $property;
            }
        }

        if (!empty($properties)) {
            $result['properties'] = $properties;
        }

        return $result;
    }


    /**
     * @param array $constraints
     * @return array
     */
    private function processConstraintsForSwagger($constraints)
    {
        $result = [];
        foreach ($constraints as $constraintType => $params) {
            $constraintClass = ConstraintCore::getConstraintClass($constraintType);
            switch ($constraintClass) {
                case Enum::class:
                    if (is_string($params)) {
                        /** @var \App\Enum\Core\Enum $enumClass */
                        $enumClass = NamespaceHelper::defineClassName(
                            $params,
                            NamespaceHelper::getEnumDefaultNamespace()
                        );
                        if (is_subclass_of($enumClass, \App\Enum\Core\Enum::class)) {
                            $result['enum'] = array_values($enumClass::getList());
                        }
                    } elseif (is_array($params)) {
                        $result['enum'] = $params;
                    }
                    break;
                case Length::class:
                    if (is_array($params)) {
                        if (array_key_exists('min', $params)) {
                            $result['minLength'] = $params['min'];
                        }
                        if (array_key_exists('max', $params)) {
                            $result['maxLength'] = $params['max'];
                        }
                    }
                    break;
                case LessThanOrEqual::class:
                    $result['maximum'] = $params;
                    break;
                case GreaterThanOrEqual::class:
                    $result['minimum'] = $params;
                    break;
            }
        }
        return $result;
    }

    /**
     * @param array $constraints
     * @param array $prefix
     * @return array
     */
    private function getConstraintComment($constraints, $prefix)
    {
        $comments = [];
        $counter = 1;
        foreach ($constraints as $constraintType => $params) {
            $constraintClass = ConstraintCore::getConstraintClass($constraintType);
            /** @var ConstraintInterface $constraint */
            $constraint = $params
                ? new $constraintClass($params)
                : new $constraintClass();

            $comments[] = $counter . '. ' . $constraint->getDocumentationComment();
            $counter++;
        }

        return [
            'en' => PHP_EOL . $prefix['en'] . PHP_EOL . implode(PHP_EOL, $comments),
            'ru' => PHP_EOL . $prefix['ru'] . PHP_EOL . implode(PHP_EOL, $comments),
        ];
    }

    /**
     * @param array $inputForm
     * @return array
     */
    private function processInputFormForValidation($inputForm)
    {
        $result = [];
        if (array_key_exists('parameters', $inputForm)) {
            $parameters = [];
            foreach ($inputForm['parameters'] as $parameter) {
                unset($parameter['description']);
                if ($parameter['type'] == 'datetime') {
                    $parameter['constraints']['DateTime'] = [];
                    $parameter['type'] = 'string';
                }
                $parameters[] = $parameter;
            }

            $result['parameters'] = $parameters;
        }

        if (array_key_exists('body', $inputForm)) {
            $result['body'] = $this->processFormObjectForValidation($inputForm['body']);
        }

        return $result;
    }

    /**
     * @param array $objectSchema
     * @return array
     */
    private function processFormObjectForValidation($objectSchema)
    {
        $result = [
            'type' => 'object',
            'properties' => [],
        ];

        foreach ($objectSchema['properties'] ?? [] as $name => $propertyData) {
            if ($propertyData['type'] == 'object') {
                $result['properties'][$name] = $this->processFormObjectForValidation($propertyData);
            } else {
                $property = DefinitionSyntaxHelper::translateTypeForForm($propertyData['type']);
                unset($propertyData['type']);
                unset($propertyData['description']);
                if (array_key_exists('format', $property)) {
                    if ($property['format'] == 'date-time') {
                        $property['constraints']['DateTime'] = [];
                        unset($property['format']);
                    }
                }
                $property = array_merge($property, $propertyData);
                $result['properties'][$name] = $property;
            }
        }

        return $result;
    }

    /**
     * @return string|array
     */
    private function processSummary()
    {
        if (!$this->currentMapKey || !$this->summary) {
            return $this->summary;
        }

        if (is_string($this->summary) && $this->summary{0} == '@') {
            $map = $this->mapping['map'][$this->currentMapKey];
            $key = substr($this->summary, 1);
            if (array_key_exists($key, $map)) {
                return $map[$key];
            }

            return '';
        }

        return $this->summary;
    }

    /**
     * @return string|array|false
     */
    private function processDescription()
    {
        if (!$this->currentMapKey || !$this->description) {
            return $this->description;
        }

        if (is_string($this->description) && $this->description{0} == '@') {
            $map = $this->mapping['map'][$this->currentMapKey];
            $key = substr($this->description, 1);
            if (array_key_exists($key, $map)) {
                return $map[$key];
            }

            return '';
        }

        return $this->description;
    }

    /**
     * @return array|false
     */
    private function processInputForm()
    {
        $this->processingType = self::INPUT_FORM_PROCESSING;
        $result = [];

        $params = ['path', 'query', 'header', 'cookie'];
        foreach ($params as $param) {
            if (array_key_exists($param, $this->inputFormMap)) {
                $processedParams = $this->processParams($this->inputFormMap[$param], $param);
                if (!$processedParams) {
                    return false;
                }

                if (!array_key_exists('parameters', $result)) {
                    $result['parameters'] = [];
                }
                $result['parameters'] = array_merge($result['parameters'], $processedParams);
            }
        }

        if (array_key_exists('body', $this->inputFormMap)) {
            $result['body'] = $this->processObjectParam($this->inputFormMap['body']);
        }

        $this->processingType = self::NONE_PROCESSING;
        return $result;
    }

    /**
     * @param array $params
     * @param string $from
     * @return array|false
     */
    private function processParams($params, $from)
    {
        $result = [];
        foreach ($params as $name => $definition) {
            $definition = DefinitionSyntaxHelper::parseAttribute(
                $definition,
                ['required' => false, 'nullable' => true]
            );
            if (!$definition) {
                $this->addErrorRow("Definition for parameter '$name' is wrong");
                return false;
            }

            if ($from == 'path') {
                $definition['required'] = true;
            }
            if ($definition['required'] === true) {
                $definition['nullable'] = false;
            }

            $definitionArray = array_merge([
                'name' => $name,
                'in' => $from,
            ], $definition);

            $constraints = $this->processConstraints($definitionArray['constraints'] ?? null);
            if (!empty($constraints)) {
                $definitionArray['constraints'] = $constraints;
            }

            if (isset($definition['items']['constraints'])) {
                $constraints = $this->processConstraints($definitionArray['items']['constraints'] ?? null);
                if (!empty($constraints)) {
                    $definitionArray['items']['constraints'] = $constraints;
                }
            }

            $result[] = $definitionArray;
        }

        return $result;
    }

    /**
     * @param array $body
     * @return array
     */
    private function processObjectParam($body)
    {
        $currentMap = $this->getCurrentMap();
        $definition = ['type' => 'object'];
        $properties = [];
        foreach ($body as $key => $value) {
            if (is_string($value) && $value{0} == '@') {
                $mapKey = substr($value, 1);
                if (!array_key_exists($mapKey, $currentMap)) {
                    continue;
                }

                $value = $currentMap[$mapKey];
            }

            if ($key{0} == '$') {
                if ($key == '$description') {
                    $definition['description'] = $value;
                } elseif ($key == '$entity') {
                    $entityParams = $this->processEntityParam($value);
                    if ($entityParams !== null) {
                        $properties = array_merge($properties, $entityParams);
                    }
                } elseif ($key == '$entities') {
                    foreach ($value as $entityData) {
                        $entityParams = $this->processEntityParam($entityData);
                        if ($entityParams !== null) {
                            $properties = array_merge($properties, $entityParams);
                        }
                    }
                } elseif ($key == '$concat') {
                    foreach ($value as &$item) {
                        $item = $this->processParam($item);
                    }
                    unset($item);
                    $properties = array_merge($properties, $value);
                } elseif ($key == '$macros') {
                    preg_match('/([^\(]+?)\(([^\)]*?)\)/', $value, $matches);
                    if (empty($matches)) {
                        continue;
                    }

                    $macrosName = $matches[1];
                    $macrosParam = $matches[2];
                    $macrosClass = NamespaceHelper::defineClassName(
                        $macrosName,
                        NamespaceHelper::getValidationFormMacrosDefaultNamespace()
                    );
                    if (!class_exists($macrosClass)) {
                        continue;
                    }

                    /** @var AbstractMacros $macros */
                    $macros = new $macrosClass;
                    $array = $macros->run($macrosParam);
                    $param = $this->processParam($array);
                    if ($param !== null && $param['type']??null == 'object') {
                        $properties = array_merge($properties, $param['properties']??[]);
                    }
                }
            } else {
                $param = $this->processParam($value);
                if ($param !== null) {
                    $properties[$key] = $param;
                }
            }
        }

        $definition['properties'] = $properties;
        return $definition;
    }

    /**
     * @param array|string $value
     * @return array|null
     */
    private function processEntityParam($value)
    {
        if (is_string($value)) {
            $value = ['$name' => $value];
        }

        if (!array_key_exists('$name', $value)) {
            return null;
        }

        $currentMap = $this->getCurrentMap();
        $entityName = $value['$name'];
        if ($entityName{0} == '@') {
            $mapKey = substr($entityName, 1);
            if (array_key_exists($mapKey, $currentMap)) {
                $entityName = $currentMap[$mapKey];
            } else {
                return null;
            }
        }

        $name = NamespaceHelper::defineClassName($entityName, NamespaceHelper::getEntityDefaultNamespace());
        if (!class_exists($name) || !is_subclass_of($name, BaseEntity::class)) {
            return null;
        }

        if (array_search($entityName, $this->dependencies) === false) {
            $this->dependencies[] = $entityName;
        }

        /** @var BaseEntity $name */
        $schema = $name::getSchema();
        $attributes = $value['$attributes'] ?? null;
        $except = $value['$except'] ?? [];
        if (is_string($except)) {
            $mapKey = substr($except, 1);
            $except = $currentMap[$mapKey] ?? [];
        }

        $withHidden = $value['$withHidden'] ?? [];
        $attributeNames = $schema->getAttributeNames();
        if (is_array($attributes)) {
            $attributeNames = array_intersect($attributes, $attributeNames);
        }
        if (!empty($except)) {
            $attributeNames = array_diff($attributeNames, $except);
        }

        $attributeDescriptions = [];
        $attributeDescriptionsPath = Mif::getProjectDir()
            . EntityGeneratorHelper::getDescriptionEntityPath($entityName);
        if (file_exists($attributeDescriptionsPath)) {
            $data = Yaml::parseFile($attributeDescriptionsPath);
            $attributeDescriptions = $data['attributes'];
        }

        $result = [];
        foreach ($attributeNames as $name) {
            $attribute = $schema->getAttribute($name);
            if ($this->inInputProcessing() && $attribute->isHiddenForInputForm()) {
                if (!in_array($name, $withHidden)) {
                    continue;
                }
            } elseif ($this->inOutputProcessing() && $attribute->isHiddenForOutputForm()) {
                if (!in_array($name, $withHidden)) {
                    continue;
                }
            }

            $definition = ['type' => $attribute->getType()];
            $descriptionArray = ['en' => [], 'ru' => []];
            if (array_key_exists($name, $attributeDescriptions)) {
                $description = $attributeDescriptions[$name];
                $descriptionArray['en'][] = is_string($description) ? $description : ($description['en'] ?? '');
                $descriptionArray['ru'][] = is_string($description) ? $description : ($description['ru'] ?? '');
            }

            $attrDescription = $attribute->getDocumentationComment();
            if ($attrDescription) {
                $descriptionArray['en'][] = is_string($attrDescription) ? $attrDescription
                    : ($attrDescription['en'] ?? '');
                $descriptionArray['ru'][] = is_string($attrDescription) ? $attrDescription
                    : ($attrDescription['ru'] ?? '');
            }

            if (!empty($descriptionArray['en'])) {
                $descriptionArray['en'] = implode(PHP_EOL, $descriptionArray['en']);
                $descriptionArray['ru'] = implode(PHP_EOL, $descriptionArray['ru']);
                $definition['description'] = $descriptionArray;
            }

            if ($this->metaData['Method'] == 'PATCH') {
                if ($attribute->isRequired()) {
                    $definition['required'] = false;
                    $definition['nullable'] = false;
                } else {
                    $definition['required'] = false;
                    $definition['nullable'] = true;
                }
            } else {
                if ($attribute->isRequired()) {
                    $definition['required'] = true;
                    $definition['nullable'] = false;
                } else {
                    $definition['required'] = false;
                    $definition['nullable'] = true;
                }
            }

            $constraints = $attribute->getConstraints();
            if (!empty($constraints)) {
                $definition['constraints'] = $constraints;
            }

            $result[$name] = $definition;
        }

        if (array_key_exists('$relations', $value)) {
            $relations = $value['$relations'];
            if (is_string($relations)) {
                if ($relations == 'all') {
                    $relations = $schema->getRelationNames();
                } elseif ($relations[0] == '@') {
                    $mapKey = substr($relations, 1);
                    $relations = $currentMap[$mapKey] ?? [];
                }
            }

            foreach ($relations as $relationName) {
                $relation = $schema->getRelation($relationName);
                if (!$relation) {
                    //TODO warning
                    continue;
                }

                if ($relation->isToMany()) {
                    $definition = [
                        'type' => 'integer[]',
                        'description' => 'Массив идентификаторов (id) связанных сущностей',
                    ];
                } else {
                    $definition = [
                        'type' => 'integer',
                        'description' => 'Идентификатор (id) связанной сущности',
                    ];
                }
                $definition['required'] = false;
                $definition['nullable'] = true;
                $result[$relationName] = $definition;
            }
        }

        return $result;
    }

    /**
     * @param string|array $value
     * @return array|null
     */
    private function processNotObjectParam($value)
    {
        $definition = DefinitionSyntaxHelper::parseAttribute($value, ['required' => false, 'nullable' => true]);
        if (!$definition) {
            return null;
        }

        if (array_key_exists('items', $definition)) {
            $definition['items'] = $this->processParam($definition['items']);
        }

        $constraints = $this->processConstraints($definition['constraints'] ?? null);
        if (!empty($constraints)) {
            $definition['constraints'] = $constraints;
        }

        return $definition;
    }

    /**
     * @param string|array $param
     * @return array|null
     */
    private function processParam($param)
    {
        if (is_string($param)) {
            return $this->processNotObjectParam($param);
        }

        if (is_array($param)) {
            if (array_key_exists('$type', $param)) {
                return $this->processNotObjectParam($param);
            }

            return $this->processObjectParam($param);
        }

        return null;
    }

    /**
     * @param array $constraints
     * @return array
     */
    private function processConstraints($constraints)
    {
        if (!$constraints) {
            return [];
        }

        $result = [];
        foreach ($constraints as $constraint) {
            preg_match('/^([^\(]+?)\(([^\)]*?)\)$/', $constraint, $matches);
            if (empty($matches)) {
                continue;
            }
            $constraintName = $matches[1];
            $constraintParams = $matches[2];
            if ($constraintParams == '') {
                $result[$constraintName] = [];
                continue;
            }

            $constraintParams = preg_split('/\s*,\s*/', $constraintParams);
            $constraintParamsArray = [];
            foreach ($constraintParams as $constraintParam) {
                $pare = preg_split('/\s*=\s*/', $constraintParam);
                $key = null;
                $value = null;
                if (count($pare) == 1) {
                    $key = count($constraintParamsArray);
                    $value = DefinitionSyntaxHelper::stringToValue($pare[0]);
                } elseif (count($pare) == 2) {
                    $key = $pare[0];
                    $value = DefinitionSyntaxHelper::stringToValue($pare[1]);
                }
                if ($key === null) {
                    continue;
                }
                $constraintParamsArray[$key] = $value;
            }
            if (count($constraintParamsArray) == 1 && array_key_exists(0, $constraintParamsArray)) {
                $constraintParamsArray = $constraintParamsArray[0];
            }
            $result[$constraintName] = $constraintParamsArray;
        }

        return $result;
    }

    /**
     * @return array|false
     */
    private function processOutputForm()
    {
        $this->processingType = self::OUTPUT_FORM_PROCESSING;

        if (array_key_exists('$scalar', $this->outputFormMap)) {
            return $this->processNotObjectParam($this->outputFormMap['$scalar']);
        }

        $result = $this->processObjectParam($this->outputFormMap);
        $this->processingType = self::NONE_PROCESSING;

        return $result;
    }

    /**
     * @return array
     */
    private function getCurrentMap()
    {
        return $this->currentMapKey
            ? $this->mapping['map'][$this->currentMapKey]
            : [];
    }

    /**
     * @return string
     */
    private function getValidationFormFilePath()
    {
        $dir = ValidationFormHelper::getUtilCompiledPath();
        if (!file_exists($dir)) {
            mkdir($dir, 0777, true);
        }

        $targetFilePath = $dir . '/' . $this->metaData['Group']
            . '_' . $this->metaData['Method']
            . '_' . $this->metaData['Action'];
        if ($this->currentMapKey) {
            $targetFilePath .= '_' . $this->currentMapKey;
        }

        return $targetFilePath . '.json';
    }

    /**
     * @return string
     */
    private function getSwaggerFormFilePath()
    {
        $dir = Mif::getProjectDir() . SwaggerConstants::SOURCE_DIR_PATH;
        if (!file_exists($dir)) {
            mkdir($dir, 0777, true);
        }

        $targetFilePath = $dir . '/' . $this->metaData['Group']
            . '_' . $this->metaData['Method']
            . '_' . $this->metaData['Action'];
        if ($this->currentMapKey) {
            $targetFilePath .= '_' . $this->currentMapKey;
        }

        return $targetFilePath . '.json';
    }

    /**
     * @param string $swaggerFilePath
     * @param string $validationFilePath
     */
    private function setMapData($swaggerFilePath, $validationFilePath)
    {
        if (!$this->currentMapKey) {
            $this->mapData = [
                'group' => $this->formsGroupName,
                'path' => $this->metaData['Path'],
                'controller' => $this->metaData['Controller'],
                'action' => $this->metaData['Action'],
                'method' => $this->metaData['Method'],
                'swaggerFilePath' => $swaggerFilePath,
                'validationFilePath' => $validationFilePath,
            ];
        } else {
            $param = $this->mapping['by'];
            $path = str_replace('{' . $param . '}', $this->currentMapKey, $this->metaData['Path']);
            $this->mapData[$this->currentMapKey] = [
                'group' => $this->formsGroupName,
                'path' => $path,
                'controller' => $this->metaData['Controller'],
                'action' => $this->metaData['Action'],
                'actionPostfix' => $this->currentMapKey,
                'method' => $this->metaData['Method'],
                'swaggerFilePath' => $swaggerFilePath,
                'validationFilePath' => $validationFilePath,
            ];
        }
    }

    /**
     * @return bool
     */
    private function inInputProcessing()
    {
        return $this->processingType == self::INPUT_FORM_PROCESSING;
    }

    /**
     * @return bool
     */
    private function inOutputProcessing()
    {
        return $this->processingType == self::OUTPUT_FORM_PROCESSING;
    }
}