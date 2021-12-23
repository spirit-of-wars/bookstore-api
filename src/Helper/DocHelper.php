<?php

namespace App\Helper;

/**
 * Class DocHelper
 * @package App\Helper
 */
class DocHelper
{
    /**
     * @param string $comment
     * @return array
     */
    public static function parseDocComment($comment)
    {
        if ($comment === false) {
            return [];
        }

        preg_match_all("/(?:@|#)([^\n\r]+?)(?:\n|\r|\*\/)/", $comment, $matches);
        if (empty($matches[0])) {
            return [];
        }

        $rows = $matches[1];
        $map = [];
        foreach ($rows as $row) {
            preg_match('/^([^(]+?)\s*\(([^)]*?)\)\s*$/', $row, $parts);
            if (!empty($parts)) {
                if ($parts[2] == '') {
                    $map[$parts[1]] = [];
                } else {
                    preg_match_all(
                        '/(?:(\w+?)\s*=\s*({.*?})|(\w+?)\s*=\s*(.*?)(?:,|\s|$)|(".*?"))/',
                        $parts[2],
                        $matches
                    );
                    $values = [];
                    if (empty($matches[0])) {
                        $values = [$parts[2]];
                    } else {
                        foreach ($matches[0] as $i => $match) {
                            if ($matches[5][$i]) {
                                $values[] = self::translateValue($matches[5][$i]);
                            } elseif ($matches[3][$i]) {
                                $values[$matches[3][$i]] = self::translateValue($matches[4][$i]);
                            } elseif ($matches[1][$i]) {
                                $key = $matches[1][$i];
                                $values[$key] = [];
                                $value = trim(self::translateValue($matches[2][$i]), '{}');
                                $value = preg_split('/\s*,\s*/', $value);
                                foreach ($value as $item) {
                                    $values[$key][] = self::translateValue($item);
                                }
                            }
                        }
                    }
                    $map[$parts[1]] = $values;
                }
                continue;
            } else {
                $arr = preg_split('/\s+/', $row);
                $key = array_shift($arr);
                if ($key == 'return') {
                    $map[$key] = $arr;
                } elseif ($key == 'param') {
                    $paramName = null;
                    if (($arr[0] ?? ' '){0} == '$') {
                        $paramName = $arr[0];
                        unset($arr[0]);
                    } elseif (($arr[1] ?? ' '){0} == '$') {
                        $paramName = $arr[1];
                        unset($arr[1]);
                    }
                    $arr = array_values($arr);
                    if ($paramName) {
                        $map[$key][$paramName] = $arr;
                    } else {
                        $map[$key][] = $arr;
                    }
                }
            }
        }

        return $map;
    }

    /**
     * @param string $value
     * @return bool|float|int|string|null
     */
    private static function translateValue($value)
    {
        $result = trim($value , '"');
        if ($result == 'false') {
            return false;
        }
        if ($result == 'true') {
            return true;
        }
        if ($result == 'null') {
            return null;
        }
        if (is_int($result)) {
            return (int)$result;
        }
        if (is_float($result)) {
            return (float)$result;
        }
        return $result;
    }
}
