<?php

namespace App\Util\ConsoleCommandExecutor\EntityGenerator\EntityRenderer;

use App\Mif;
use App\Util\ConsoleCommandExecutor\EntityGenerator\EntityGeneratorHelper;
use Symfony\Component\Yaml\Yaml;

/**
 * Class DescriptionRenderer
 * @package App\Util\ConsoleCommandExecutor\EntityGenerator\EntityRenderer
 */
class DescriptionRenderer
{
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
        $entityName = $this->schema->getName();
        $attributes = $this->schema->getAttributes();
        $oldAttributes = $this->getOldAttributes($entityName);

        $newAttributes = [];
        foreach ($attributes as $name => $data) {
            if (array_key_exists($name, $oldAttributes)) {
                $newAttributes[$name] = $oldAttributes[$name];
                continue;
            }
            $newAttributes[$name] = [
                'ru' => '',
                'en' => '',
            ];
        }

        $text = 'name: ' . $entityName . PHP_EOL . 'attributes:' . PHP_EOL;
        foreach ($newAttributes as $name => $data) {
            $en = $data['en'];
            if (!$en) {
                $en = ucfirst(preg_replace_callback('/(.)([A-Z])/', function ($match) {
                    return $match[1] . ' ' . strtolower($match[2]);
                }, $name));
            }

            $text .= '  ' . $name . ':' . PHP_EOL;
            $text .= '    ru: ' . $data['ru'] . PHP_EOL;
            $text .= '    en: ' . $en . PHP_EOL;
        }

        $path = Mif::getProjectDir() . EntityGeneratorHelper::getDescriptionEntityPath($entityName);
        $dirName = dirname($path);
        if (!file_exists($dirName)) {
            mkdir($dirName, 0777, true);
        }
        file_put_contents($path, $text);
    }

    /**
     * @param string $entity
     * @return array
     */
    private function getOldAttributes($entity)
    {
        $path = Mif::getProjectDir() . EntityGeneratorHelper::getDescriptionEntityPath($entity);
        if (!file_exists($path)) {
            return [];
        }

        $data = Yaml::parseFile($path);
        return $data['attributes'];
    }
}
