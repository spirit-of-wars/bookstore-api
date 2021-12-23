<?php

namespace App\Util\ConsoleCommandExecutor\EntityGenerator\EntityRenderer;

use App\EntitySupport\Common\EntityConstants;
use App\EntitySupport\OrmType\DictionaryType;
use App\EntitySupport\OrmType\ListType;
use App\Enum\OrmRelationTypeEnum;
use App\Util\Common\DefinitionSyntaxHelper;
use App\Util\Common\MessageKeeperTrait;
use App\Util\ConsoleCommandExecutor\EntityGenerator\EntityGeneratorHelper;

/**
 * Class MainRenderer
 * @package App\Util\ConsoleCommandExecutor\EntityGenerator\EntityRenderer
 */
class CodeRenderer
{
    use MessageKeeperTrait;

    /** @var Schema */
    private $schema;

    /** @var string */
    private $code;

    /**
     * MainRenderer constructor.
     * @param Schema $schema
     */
    public function __construct($schema)
    {
        $this->schema = $schema;
        $this->code = '';
    }

    /**
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    public function run()
    {
        $this->addTextRow('Code generation have started...');

        $namespaceCode = $this->renderNamespaceCode();
        $useCode = $this->renderUseCode();
        $nameCode = EntityGeneratorHelper::getEntitySimpleName($this->schema->getName());
        $ormExtensionCode = $this->renderOrmExtensionCode();
        $methodsCode = $this->renderMethodsCode();
        $interfacesCode = $this->renderInterfacesCode();
        $traitsCode = $this->renderTraitsCode();
        $attributesCode = $this->renderAttributesCode();
        $relationsCode = $this->renderRelationsCode();
        $constructorCode = $this->renderConstructorCode();
        $implementsCode = $this->renderImplementationsCode();

        $code = $this->getTemplate();
        $code = str_replace('<namespace>', $namespaceCode, $code);
        $code = str_replace('<use>', $useCode, $code);
        $code = str_replace('<name>', $nameCode, $code);
        $code = str_replace('<orm_ext>', $ormExtensionCode, $code);
        $code = str_replace('<methods>', $methodsCode, $code);
        $code = str_replace('<interfaces>', $interfacesCode, $code);
        $code = str_replace('<traits>', $traitsCode, $code);
        $code = str_replace('<attributes>', $attributesCode, $code);
        $code = str_replace('<relations>', $relationsCode, $code);
        $code = str_replace('<constructor>', $constructorCode, $code);
        $code = str_replace('<implements>', $implementsCode, $code);

        $this->code = $code;
    }

    /**
     * @return string
     */
    private function renderNamespaceCode()
    {
        $namespace = EntityGeneratorHelper::getEntityNamespace($this->schema->getName());
        return $namespace == ''
            ? $namespace
            : '\\' . $namespace;
    }

    /**
     * @return string
     */
    private function renderUseCode()
    {
        $forUse = $this->schema->getForUse();
        if (empty($forUse)) {
            return '';
        }

        $arr = [];
        foreach ($forUse as $item) {
            if (is_string($item)) {
                $row = 'use ' . $item . ';';
                if (array_search($row, $arr) === false) {
                    $arr[] = $row;
                }
            } elseif (is_array($item)
                && array_key_exists('className', $item)
                && array_key_exists('as', $item)
            ) {
                $row = 'use ' . $item['className'] . ' as ' . $item['as'] . ';';
                if (array_search($row, $arr) === false) {
                    $arr[] = $row;
                }
            }
        }
        return PHP_EOL . implode(PHP_EOL, $arr);
    }

    /**
     * @return string
     */
    private function renderOrmExtensionCode()
    {
        $ext = array_merge(
            $this->checkOrmTableName(),
            $this->schema->getOrmExtensions()
        );
        if (empty($ext)) {
            return '';
        }

        $arr = [];
        foreach ($ext as $item) {
            $arr[] = ' * ' . $item;
        }
        return PHP_EOL . implode(PHP_EOL, $arr);
    }

    /**
     * @return array
     */
    private function checkOrmTableName()
    {
        $tableName = $this->schema->getTableName();
        if ($tableName == '') {
            $name = EntityGeneratorHelper::getEntitySimpleName($this->schema->getName());
            $tableName = lcfirst(preg_replace_callback('/(.)([A-Z])/', function ($match) {
                return $match[1] . '_' . strtolower($match[2]);
            }, $name));
        }

        $tableNameArray = explode('.', $tableName);
        $tableName = array_pop($tableNameArray);

        $namespace = EntityGeneratorHelper::getEntityNamespace($this->schema->getName());
        $tableSchemaName = empty($tableNameArray) ? '' : (array_pop($tableNameArray) . '.');
        if ($tableSchemaName == '' && $namespace != '') {
            $tableSchemaName = lcfirst(preg_replace_callback('/(.)([A-Z])/', function ($match) {
                return $match[1] . '_' . strtolower($match[2]);
            }, $namespace)) . '.';
        }

        if ($tableSchemaName == '') {
            $tableSchemaName = 'common.';
        }

        return ['@ORM\\Table(name="' . $tableSchemaName . $tableName . '")'];
    }

    /**
     * @return string
     */
    private function renderMethodsCode()
    {
        $attributes = array_merge($this->schema->getAttributes(), $this->schema->getExtraAttributes());
        $relations = $this->schema->getRelations();
        $set = EntityConstants::ACTION_SET;
        $get = EntityConstants::ACTION_GET;
        $getItem = EntityConstants::ACTION_GET_ITEM;
        $add = EntityConstants::ACTION_ADD;
        $remove = EntityConstants::ACTION_REMOVE;

        $getters = [' * @method integer getId()'];
        $setters = [];
        $otherMethods = [];
        foreach ($attributes as $name => $item) {
            $ucName = ucfirst($name);
            $originType = $item['orm']['type'];
            $type = DefinitionSyntaxHelper::translateTypeForCode($originType);
            $getters[] = " * @method {$type} {$get}{$ucName}()";
            if ($item['readonly']) {
                continue;
            }
            $setters[] = " * @method \$this {$set}{$ucName}({$type} \${$name})";
            switch ($originType) {
                case ListType::NAME:
                    $getters[] = " * @method mixed {$getItem}{$ucName}(integer \$index)";
                    $otherMethods[] = " * @method \$this {$add}{$ucName}(mixed \$value)";
                    $otherMethods[] = " * @method \$this {$remove}{$ucName}(mixed \$value)";
                    break;
                case DictionaryType::NAME:
                    $getters[] = " * @method mixed {$getItem}{$ucName}(string \$key)";
                    $otherMethods[] = " * @method \$this {$add}{$ucName}(string|array \$keyOrMap, mixed \$value = null)";
                    $otherMethods[] = " * @method \$this {$remove}{$ucName}(string \$key)";
                    break;
            }
        }

        foreach ($relations as $relationName => $relation) {
            $type = $relation['relAlias'];
            $ucName = ucfirst($relationName);
            switch ($relation['type']) {
                case OrmRelationTypeEnum::ONE_TO_ONE:
                case OrmRelationTypeEnum::MANY_TO_ONE:
                    $getters[] = " * @method {$type} {$get}{$ucName}()";
                    $setters[] = " * @method \$this {$set}{$ucName}({$type} \${$relationName})";
                    break;
                case OrmRelationTypeEnum::ONE_TO_MANY:
                case OrmRelationTypeEnum::MANY_TO_MANY:
                    $getters[] = " * @method Collection {$get}{$ucName}()";
                    $relName = lcfirst($type);
                    $otherMethods[] = " * @method \$this {$add}{$ucName}({$type} \${$relName})";
                    $otherMethods[] = " * @method \$this {$remove}{$ucName}({$type} \${$relName})";
                    break;
            }
        }

        $result = implode(PHP_EOL, $getters);
        if (!empty($setters)) {
            $result .= PHP_EOL . ' *' . PHP_EOL . implode(PHP_EOL, $setters);
        }
        if (!empty($otherMethods)) {
            $result .= PHP_EOL . ' *' . PHP_EOL . implode(PHP_EOL, $otherMethods);
        }
        return $result;
    }

    /**
     * @return string
     */
    private function renderInterfacesCode()
    {
        $list = $this->schema->getInterfaces();
        if (empty($list)) {
            return '';
        }

        return PHP_EOL
            . '    implements' . PHP_EOL
            . '    ' . implode(',' . PHP_EOL . '    ', $list);
    }

    /**
     * @return string
     */
    private function renderTraitsCode()
    {
        $forTraits = $this->schema->getForTraits();
        if (empty($forTraits)) {
            return '';
        }

        $arr = [];
        foreach ($forTraits as $item) {
            $arr[] = '    use ' . $item . ';';
        }
        return PHP_EOL . implode(PHP_EOL, $arr);
    }

    /**
     * @return string
     */
    private function renderAttributesCode()
    {
        $attributes = $this->schema->getAttributes();
        $attributesArr = [];
        foreach ($attributes as $name => $value) {
            $attributesArr[] = $this->renderAttributeCode($name, $value);
        }
        return implode(PHP_EOL . PHP_EOL, $attributesArr);
    }

    /**
     * @return string
     */
    private function renderRelationsCode()
    {
        $relations = $this->schema->getRelations();
        if (empty($relations)) {
            return '';
        }

        $relationsArr = [];
        foreach ($relations as $name => $value) {
            $relationsArr[] = $this->renderRelationCode($name, $value);
        }
        return PHP_EOL . PHP_EOL . implode(PHP_EOL . PHP_EOL, $relationsArr);
    }

    /**
     * @return string
     */
    private function renderConstructorCode()
    {
        $relations = $this->schema->getRelations();
        if (empty($relations)) {
            return '';
        }

        $list = [];
        foreach ($relations as $relationName => $relation) {
            if ($relation['type'] == OrmRelationTypeEnum::ONE_TO_ONE
                || $relation['type'] == OrmRelationTypeEnum::MANY_TO_ONE
            ) {
                continue;
            }

            $list[] = $relationName;
        }

        if (empty($list)) {
            return '';
        }

        $rows = ['    public function __construct()', '    {'];
        foreach ($list as $field) {
            $rows[] = '        $this->' . $field . ' = new ArrayCollection();';
        }
        $rows[] = '    }';

        return PHP_EOL . PHP_EOL . implode(PHP_EOL, $rows);
    }

    /**
     * @return string
     */
    private function renderImplementationsCode()
    {
        $result = '';

        $attributes = $this->schema->getAttributes();
        $readOnlyAttributes = [];
        foreach ($attributes as $name => $item) {
            if ($item['readonly']) {
                $readOnlyAttributes[] = "            '$name',";
            }
        }
        if (!empty($readOnlyAttributes)) {
            $string = implode(PHP_EOL, $readOnlyAttributes);
            $readOnlyTemplate = $this->getReadOnlyTemplate();
            $result .= PHP_EOL . PHP_EOL . str_replace('<attributes>', $string, $readOnlyTemplate);
        }

        return $result;
    }

    /**
     * @param string $name
     * @param array $definition
     * @return string
     */
    private function renderAttributeCode($name, $definition)
    {
        $ormArr = [];
        $default = null;
        foreach ($definition['orm'] as $key => $value) {
            if (is_numeric($value)) {
                $value = '' . $value;
            } elseif (is_string($value)) {
                $value = '"' . $value . '"';
            } elseif ($value === true) {
                $value = 'true';
            } elseif ($value === false) {
                $value = 'false';
            } elseif (is_array($value)) {
                $value = json_encode($value);
            } else {
                continue;
            }

            if ($key == 'default') {
                $default = $value;
                continue;
            }

            $ormArr[] = "$key=$value";
        }
        $ormString = '@ORM\Column('. implode(', ', $ormArr) . ')';

        if (empty($definition['app'])) {
            $doc = '    /** ' . $ormString . ' */';
        } else {
            $doc = '    /**' . PHP_EOL . '     * ' . $ormString . PHP_EOL;
            foreach ($definition['app'] as $appDefinition) {
                $doc .= '     * #' . $appDefinition . PHP_EOL;
            }
            $doc .= '     */';
        }

        $var = '    private $' . $name;
        if ($default) {
            $var .= ' = ' . $default;
        }
        $var .= ';';
        return $doc . PHP_EOL . $var;
    }

    /**
     * @param string $name
     * @param array $definition
     * @return string
     */
    private function renderRelationCode($name, $definition)
    {
        switch ($definition['type']) {
            case OrmRelationTypeEnum::ONE_TO_ONE:
                return $this->renderRelationOneToOne($name, $definition);

            case OrmRelationTypeEnum::MANY_TO_ONE:
                return $this->renderRelationManyToOne($name, $definition);

            case OrmRelationTypeEnum::ONE_TO_MANY:
                return $this->renderRelationOneToMany($name, $definition);

            case OrmRelationTypeEnum::MANY_TO_MANY:
                return $this->renderRelationManyToMany($name, $definition);
        }

        return '';
    }

    /**
     * @param string $name
     * @param array $definition
     * @return string
     */
    private function renderRelationOneToOne($name, $definition)
    {
        if ($definition['uni']) {
            return '    /** @ORM\OneToOne(targetEntity="'
                . $definition['relClassName']
                . '") */' . PHP_EOL
                . '    private $' . $name . ';';
        } else {
            $inversed = $definition['fkHost'];
            if ($inversed) {
                return '    /** @ORM\OneToOne(targetEntity="'
                    . $definition['relClassName']
                    . '", inversedBy="' . $definition['relAttribute']
                    . '") */' . PHP_EOL
                    . '    private $' . $name . ';';
            } else {
                return '    /** @ORM\OneToOne(targetEntity="'
                    . $definition['relClassName']
                    . '", mappedBy="' . $definition['relAttribute']
                    . '") */' . PHP_EOL
                    . '    private $' . $name . ';';
            }
        }
    }

    /**
     * @param string $name
     * @param array $definition
     * @return string
     */
    private function renderRelationManyToOne($name, $definition)
    {
        if ($definition['uni']) {
            return '    /** @ORM\ManyToOne(targetEntity="'
                . $definition['relClassName']
                . '") */' . PHP_EOL
                . '    private $' . $name . ';';
        } else {
            return '    /** @ORM\ManyToOne(targetEntity="'
                . $definition['relClassName']
                . '", inversedBy="' . $definition['relAttribute']
                . '") */' . PHP_EOL
                . '    private $' . $name . ';';
        }
    }

    /**
     * @param string $name
     * @param array $definition
     * @return string
     */
    private function renderRelationOneToMany($name, $definition)
    {
        if ($definition['orphanRemoval']) {
            return '    /** @ORM\OneToMany(targetEntity="'
                . $definition['relClassName']
                . '", mappedBy="' . $definition['relAttribute']
                . '", orphanRemoval=true) */' . PHP_EOL
                . '    private $' . $name . ';';
        } else {
            return '    /** @ORM\OneToMany(targetEntity="'
                . $definition['relClassName']
                . '", mappedBy="' . $definition['relAttribute']
                . '") */' . PHP_EOL
                . '    private $' . $name . ';';
        }
    }

    /**
     * @param string $name
     * @param array $definition
     * @return string
     */
    private function renderRelationManyToMany($name, $definition)
    {
        $entityName = $this->schema->getName();
        if ($entityName == $definition['relEntity']) {
            return $this->renderRelationSelfManyToMany($name, $definition);
        }

        $inversed = ($entityName < $definition['relEntity']);
        if ($inversed) {
            $tableName = 'util_rel.'
                . lcfirst(preg_replace_callback('/(.)([A-Z])/', function ($match) {
                    return $match[1] . '_' . strtolower($match[2]);
                }, str_replace('\\', '_', $entityName)) ) . '__'
                . lcfirst(preg_replace_callback('/(.)([A-Z])/', function ($match) {
                    return $match[1] . '_' . strtolower($match[2]);
                }, str_replace('\\', '_', $definition['relEntity']) ));
            return '    /**' . PHP_EOL
                . '     * @ORM\ManyToMany(targetEntity="' . $definition['relClassName']
                . '", inversedBy="' . $definition['relAttribute'] . '")' . PHP_EOL
                . '     * @ORM\JoinTable(name="' . $tableName . '")' . PHP_EOL
                . '     */' . PHP_EOL
                . '    private $' . $name . ';';
        } else {
            return '    /** @ORM\ManyToMany(targetEntity="' . $definition['relClassName']
                . '", mappedBy="' . $definition['relAttribute'] . '") */' . PHP_EOL
                . '    private $' . $name . ';';
        }
    }

    /**
     * @param string $name
     * @param array $definition
     * @return string
     */
    private function renderRelationSelfManyToMany($name, $definition)
    {
        $entityName = $this->schema->getName();
        $inversed = ($name < $definition['relAttribute']);
        if ($inversed) {
            $snakeName = lcfirst(preg_replace_callback('/(.)([A-Z])/', function ($match) {
                return $match[1] . '_' . strtolower($match[2]);
            }, $name));
            $relSnakeName = lcfirst(preg_replace_callback('/(.)([A-Z])/', function ($match) {
                return $match[1] . '_' . strtolower($match[2]);
            }, $definition['relAttribute']));
            $tableName = 'util_rel.'
                . lcfirst(preg_replace_callback('/(.)([A-Z])/', function ($match) {
                    return $match[1] . '_' . strtolower($match[2]);
                }, str_replace('\\', '_', $entityName))) . '__' . $snakeName . '__' . $relSnakeName;
            $column = $snakeName . '_id';
            $relColumn = $relSnakeName . '_id';
            return '    /**' . PHP_EOL
                . '     * @ORM\ManyToMany(targetEntity="' . $definition['relClassName']
                . '", inversedBy="' . $definition['relAttribute'] . '")' . PHP_EOL
                . '     * @ORM\JoinTable(name="' . $tableName . '",' . PHP_EOL
                . '     *     joinColumns={@ORM\JoinColumn(name="' . $column . '", referencedColumnName="id")},' . PHP_EOL
                . '     *     inverseJoinColumns={@ORM\JoinColumn(name="'
                . $relColumn . '", referencedColumnName="id")}' . PHP_EOL
                . '     * )' . PHP_EOL
                . '     */' . PHP_EOL
                . '    private $' . $name . ';';
        } else {
            return '    /** @ORM\ManyToMany(targetEntity="' . $definition['relClassName']
                . '", mappedBy="' . $definition['relAttribute'] . '") */' . PHP_EOL
                . '    private $' . $name . ';';
        }
    }

    /**
     * @return string
     */
    private function getTemplate()
    {
        return file_get_contents(__DIR__ . '/tpl/template');
    }

    /**
     * @return string
     */
    private function getReadOnlyTemplate()
    {
        return file_get_contents(__DIR__ . '/tpl/readonlyTemplate');
    }
}
