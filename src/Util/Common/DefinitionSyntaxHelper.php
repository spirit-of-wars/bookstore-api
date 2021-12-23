<?php

namespace App\Util\Common;

use App\EntitySupport\Common\EntityConstants;
use App\EntitySupport\OrmType\DictionaryType;
use App\EntitySupport\OrmType\ListType;

/**
 * Class DefinitionSyntaxHelper
 * @package App\Util\Common
 */
class DefinitionSyntaxHelper
{
    /**
     * @param string $type
     * @return string
     */
    public static function translateTypeForCode($type)
    {
        $map = self::getTypesMap();
        return $map[$type]['code'] ?? $type;
    }

    /**
     * @param string $type
     * @return array
     */
    public static function translateTypeForForm($type)
    {
        $map = self::getTypesMap();
        return $map[$type]['form'] ?? ['type' => $type];
    }

    /**
     * @param array|string $definition
     * @param array $states
     * @return array|false
     */
    public static function parseAttribute($definition, $states = [])
    {
        $result = [];
        if (is_array($definition)) {
            if (array_key_exists('$description', $definition)) {
                $result['description'] = $definition['$description'];
            }

            if (array_key_exists('$definition', $definition)) {
                $def = self::parseAttribute($definition['$definition'], $states);
                if (!$def) {
                    return false;
                }
                $result = array_merge($def, $result);
                return $result;
            }

            if (array_key_exists('$type', $definition)) {
                $result['type'] = $definition['$type'];
            }
            if (array_key_exists('$default', $definition)) {
                $result['default'] = $definition['$default'];
            }
            if (array_key_exists('$example', $definition)) {
                $result['example'] = $definition['$example'];
            }
            if (array_key_exists('$constraints', $definition)) {
                $result['constraints'] = (array)$definition['$constraints'];
            }
            if (array_key_exists('$flags', $definition)) {
                $result['flags'] = $definition['$flags'];
            }
            if (array_key_exists('$items', $definition)) {
                $items = $definition['$items'];
                if (array_key_exists('$type', $items)) {
                    $items['type'] = $items['$type'];
                    unset($items['$type']);
                }
                if (array_key_exists('$constraints', $items)) {
                    $items['constraints'] = (array)$items['$constraints'];
                    unset($items['$constraints']);
                }
                $result['items'] = $items;
            }
            foreach ($states as $stateName => $stateDefault) {
                if (array_key_exists('$' . $stateName, $definition)) {
                    $result[$stateName] = $definition['$' . $stateName];
                } else {
                    $result[$stateName] = $stateDefault;
                }
            }
        } elseif (is_string($definition)) {
            $reg = '/(?:-\b\w+?\b|string\[\]|float\[\]|integer\[\]|\[[^\]]*?\]|[!\w]+?\([^)]*?\)|[!\w]+)/';
            preg_match_all($reg, $definition, $matches);
            if (empty($matches[0])) {
                return false;
            }

            $definitionArray = $matches[0];
            $constraints = [];
            foreach ($definitionArray as $definitionItem) {
                if ($definitionItem[0] == '-') {
                    $result['flags'][] = $definitionItem;
                    continue;
                }

                if (preg_match('/^default\((.+)\)$/', $definitionItem, $matches)) {
                    $result['default'] = self::stringToValue($matches[1]);
                    continue;
                }

                if (preg_match('/^example\((.+)\)$/', $definitionItem, $matches)) {
                    $result['example'] = self::stringToValue($matches[1]);
                    continue;
                }

                // required nullable
                if (array_key_exists($definitionItem, $states)) {
                    $result[$definitionItem] = true;
                    continue;
                }

                // !required !nullable
                if ($definitionItem{0} == '!') {
                    $stateKey = substr($definitionItem, 1);
                    if (array_key_exists($stateKey, $states)) {
                        $result[$stateKey] = false;
                        continue;
                    }
                }

                // hidden inputHidden outputHidden
                if ($definitionItem == EntityConstants::HIDDEN_FIELD_FOR_FORM
                    || $definitionItem == EntityConstants::HIDDEN_FIELD_FOR_INPUT_FORM
                    || $definitionItem == EntityConstants::HIDDEN_FIELD_FOR_OUTPUT_FORM
                ) {
                    $result['inForm'] = $definitionItem;
                    continue;
                }

                // Enum("SomeEnum")
                if (preg_match('/^[A-Z]\w*?\(.*?\)/', $definitionItem)) {
                    $constraints[] = $definitionItem;
                    continue;
                }

                // [5-10]
                if (preg_match('/^\[(.*?)\-(.*?)\]$/', $definitionItem, $matches)) {
                    if ($matches[1] != '') {
                        $constraints[] = "GreaterThanOrEqual({$matches[1]})";
                    }
                    if ($matches[2] != '') {
                        $constraints[] = "LessThanOrEqual({$matches[2]})";
                    }
                    continue;
                }

                $result['type'] = $definitionItem;
            }

            foreach ($states as $stateName => $stateDefault) {
                if (!array_key_exists($stateName, $result)) {
                    $result[$stateName] = $stateDefault;
                }
            }

            if (!empty($constraints)) {
                $result['constraints'] = $constraints;
            }
        }

        if (!isset($result['type'])) {
            return false;
        }

        if (isset($result['required']) && isset($result['nullable']) && $result['required'] && $result['nullable']) {
            $result['nullable'] = false;
        }

        return $result;
    }

    /**
     * @param string $str
     * @return bool|float|int|null|string
     */
    public static function stringToValue($str)
    {
        if ($str == 'null') {
            return null;
        }
        if ($str == 'false') {
            return false;
        }
        if ($str == 'true') {
            return true;
        }
        if (is_numeric($str)) {
            if (strpos($str, '.') !== false) {
                return (float)$str;
            }
            return (integer)$str;
        }
        return trim($str, '"');
    }

    /**
     * @return array
     */
    private static function getTypesMap()
    {
        /* Default types:
         * - integer
         * - float
         * - string
         * - boolean
         * */
        return [
            ListType::NAME => [
                'code' => 'array',
                'form' => [
                    'type' => 'array',
                    'items' => [
                        'type' => 'string',
                    ],
                ],
            ],

            DictionaryType::NAME => [
                'code' => 'array',
                'form' => [
                    'type' => 'object',
                    'properties' => [],
                ],
            ],

            'text' => [
                'code' => 'string',
                'form' => ['type' => 'string'],
            ],

            'smallint' => [
                'code' => 'integer',
                'form' => ['type' => 'integer'],
            ],

            'bigint' => [
                'code' => 'integer',
                'form' => ['type' => 'integer'],
            ],

            'datetime' => [
                'code' => 'DateTime',
                'form' => [
                    'type' => 'string',
                    'format' => 'date-time',
                ],
            ],

            'object' => [
                'code' => 'array',
                'form' => ['type' => 'object'],
            ],

            'integer[]' => [
                'code' => 'array',
                'form' => [
                    'type' => 'array',
                    'items' => [
                        'type' => 'integer',
                    ],
                ],
            ],

            'float[]' => [
                'code' => 'array',
                'form' => [
                    'type' => 'array',
                    'items' => [
                        'type' => 'float',
                    ],
                ],
            ],

            'string[]' => [
                'code' => 'array',
                'form' => [
                    'type' => 'array',
                    'items' => [
                        'type' => 'string',
                    ],
                ],
            ],
        ];
    }
}
