<?php

namespace App\Util\ConsoleCommandExecutor\ValidationForm;

use App\Mif;
use App\Util\ConsoleCommandExecutor\EntityGenerator\EntityGeneratorHelper;

/**
 * Class TaskContext
 * @package App\Util\ConsoleCommandExecutor\ValidationForm
 */
class TaskContext extends \App\Util\Task\TaskContext
{
    /**
     * @return array|null
     */
    public function getFormsMap()
    {
        $map = $this->getData('map');
        if ($map) {
            return $map;
        }

        $path = ValidationFormHelper::getMapFilePath();
        if (!file_exists($path)) {
            return null;
        }

        $map = json_decode(file_get_contents($path), true);
        $this->addData('map', $map);
        return $map;
    }

    /**
     * @param string $key
     * @param array $value
     */
    public function addToFormsMap($key, $value)
    {
        $map = $this->getFormsMap() ?? [];
        $map[$key] = $value;
        $this->addData('map', $map);
    }

    /**
     * @return void
     */
    public function saveMap()
    {
        $path = ValidationFormHelper::getUtilCompiledPath();
        if (!file_exists($path)) {
            mkdir($path, 0777, true);
        }

        $path .= '/' . ValidationFormHelper::getMapFileName();
        file_put_contents($path, json_encode($this->getFormsMap() ?? [], JSON_PRETTY_PRINT));
    }

    /**
     * @param array $metaData
     * @return array
     */
    public function getCompiledFormFileNames($metaData)
    {
        $map = $this->getFormsMap();
        if (!$map) {
            return [];
        }

        $key = $this->getKeyForFormsMap($metaData);
        if (!array_key_exists($key, $map)) {
            return [];
        }

        if (array_key_exists('mapping', $map[$key])) {
            $result = [];
            foreach ($map[$key]['mapping']['map'] as $item) {
                $result[] = $item['validationFilePath'];
            }
        } else {
            $result = [$map[$key]['validationFilePath']];
        }

        foreach ($result as &$item) {
            $item = Mif::getProjectDir() . $item;
        }
        unset($item);

        return $result;
    }

    /**
     * @param $metaData
     * @return array
     */
    public function getEntityFileNames($metaData)
    {
        $map = $this->getFormsMap();
        if (!$map) {
            return [];
        }

        $key = $this->getKeyForFormsMap($metaData);
        if (!array_key_exists($key, $map)) {
            return [];
        }

        if (key_exists('dependencies', $map[$key])) {
            $result = [];
            foreach ($map[$key]['dependencies'] as $item){
                $result[] = Mif::getProjectDir() . EntityGeneratorHelper::getEntityPath($item);
            }
            return $result;
        }

        return [];
    }

    /**
     * @param array $metaData
     * @return string
     */
    public function getKeyForFormsMap($metaData)
    {
        return $metaData['Controller']
            . '::' . $metaData['Action']
            . '::' . strtolower($metaData['Method']);
    }
}
