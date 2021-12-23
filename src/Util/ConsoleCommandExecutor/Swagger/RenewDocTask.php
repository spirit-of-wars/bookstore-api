<?php

namespace App\Util\ConsoleCommandExecutor\Swagger;

use App\Enum\ApiMethodEnum;
use App\Mif;
use App\Util\Task\Task;
use App\Util\ConsoleCommandExecutor\ValidationForm\ValidationFormHelper;

/**
 * Class RenewDocTask
 * @package App\Util\ConsoleCommandExecutor\Swagger
 */
class RenewDocTask extends Task
{
    const YAML_LINK = 'https://bitbucket.org/mifbitbucket2/mif-api/src/develop/util/Entity/';

    /** @var array */
    private $pathsMap = [];

    protected function run()
    {
        $this->addTitle('* Task: renew Swagger documentation');

        $formsMap = $this->loadFormsMap();
        if ($formsMap === false) {
            return;
        }

        foreach ($formsMap as $apiQueryData) {
            if (array_key_exists('mapping', $apiQueryData)) {
                foreach ($apiQueryData['mapping']['map'] as $data) {
                    if (!$this->processApiQueryData($data)) {
                        return;
                    }
                }
            } else {
                if (!$this->processApiQueryData($apiQueryData)) {
                    return;
                }
            }
        }

        $headerPath = SwaggerConstants::getHeaderPath();
        if (!file_exists($headerPath)) {
            $this->addErrorRow('Swagger header file doesn\'t exist');
            return;
        }
        $componentsPath = SwaggerConstants::getComponentsPath();
        if (!file_exists($componentsPath)) {
            $this->addErrorRow('Swagger components file doesn\'t exist');
            return;
        }

        $swaggerDoc = json_decode(file_get_contents($headerPath), true);
        $swaggerDoc['paths'] = $this->pathsMap;
        $components = json_decode(file_get_contents($componentsPath), true);
        $swaggerDoc['components'] = $components;
        $this->createDocFile($swaggerDoc, 'en');
        $this->createDocFile($swaggerDoc, 'ru');
    }

    /**
     * @param array $swaggerDoc
     * @param string $lang
     */
    private function createDocFile($swaggerDoc, $lang)
    {
        $swaggerDocRu = $this->applyLang($swaggerDoc, $lang);
        if (isset($swaggerDocRu['info']['description'])) {
            $swaggerDocRu['info']['description'] = '<span id="mif-desc">'
                .  $swaggerDocRu['info']['description'] . '</span>';
        }
        $path = Mif::getProjectDir() . '/swagger/' . $lang . '.json';
        file_put_contents($path, json_encode($swaggerDocRu, JSON_PRETTY_PRINT));
    }

    /**
     * @param array $apiQueryData
     * @return bool
     */
    private function processApiQueryData($apiQueryData)
    {
        if (!$this->validateApiQueryData($apiQueryData)) {
            return false;
        }

        $path = $apiQueryData['path'];
        $method = strtolower($apiQueryData['method']);
        if (isset($this->pathsMap[$path][$method])) {
            $this->addErrorRow("Here is more than one definition for path '{$path}', method '{$method}'");
            return false;
        }

        $formsFilePath = Mif::getProjectDir() . $apiQueryData['swaggerFilePath'];
        $forms = $this->loadForms($formsFilePath);
        if ($forms === false) {
            return false;
        }

        $forms = $this->enlargeFormDescription($forms, $apiQueryData);

        $id = $apiQueryData['controller'] . '::' . $apiQueryData['action'];
        if (isset($apiQueryData['actionPostfix'])) {
            $id .= '_' . $apiQueryData['actionPostfix'];
        }
        $definition = [
            'tags' => [$apiQueryData['group']],
            'operationId' => $id,
            'summary' => $forms['summary'] ?? '',
            'description' => $forms['description'] ?? '',
        ];

        $doc = $this->processInputForm($apiQueryData, $forms['inputForm'] ?? []);
        if ($doc === false) {
            return false;
        }

        $doc['responses'] = $this->processOutputForm($forms['outputForm'] ?? []);

        $this->pathsMap[$path][$method] = array_merge($definition, $doc);
        return true;
    }

    /**
     * @param array $form
     * @param array $apiQueryData
     * @return array
     */
    private function enlargeFormDescription($form, $apiQueryData)
    {
        if (empty($apiQueryData['dependencies'])) {
            return $form;
        }

        $description = (isset($form['description']) && $form['description'] != '')
            ? $form['description']
            : [
                'ru' => '',
                'en' => '',
            ];


        if (is_string($description)) {
            $description = $this->genYamlLinks($description, $apiQueryData['dependencies'], 'en');
        } elseif (is_array($description)) {
            $description['ru'] = $this->genYamlLinks($description['ru'], $apiQueryData['dependencies'], 'ru');
            $description['en'] = $this->genYamlLinks($description['en'], $apiQueryData['dependencies'], 'en');
        }

        $form['description'] = $description;
        return $form;
    }

    /**
     * @param string $description
     * @param array $dependencies
     * @param string $lang
     * @return string
     */
    private function genYamlLinks($description, $dependencies, $lang)
    {
        $links = [];
        foreach ($dependencies as $dependency) {
            $link = self::YAML_LINK . str_replace('\\', '/', $dependency) . '.yaml';
            $links[] = '<li>' . $dependency
                . ' - <a href="' . $link
                . '">' . $link . '</a></li>';
        }
        $linksText = (
            ($lang == 'en')
                ? 'Model yaml-schemas related to the request:'
                : 'Yaml-схемы моделей, имеющих отношение к запросу:'
            )
            . '<ul>'
            . implode('', $links)
            . '</ul>';

        if ($description == '') {
            return $linksText;
        }

        return $linksText . '</br></br>' . $description;
    }

    /**
     * @param array $apiQueryData
     * @param array $inputForm
     * @return array|false
     */
    private function processInputForm($apiQueryData, $inputForm)
    {
        if (array_key_exists('body', $inputForm)
            && ($apiQueryData['method'] == ApiMethodEnum::GET || $apiQueryData['method'] == ApiMethodEnum::DELETE)
        ) {
            $this->addErrorRow(
                "Body definition is not available for method {$apiQueryData['method']} in "
                . "{$apiQueryData['group']}::{$apiQueryData['action']}"
            );
            return false;
        }

        $result = [];
        if (array_key_exists('security', $inputForm)) {
            $result['security'] = $inputForm['security'];
        }

        if (array_key_exists('parameters', $inputForm)) {
            $result['parameters'] = $inputForm['parameters'];
        }

        if (array_key_exists('body', $inputForm)) {
            $result['requestBody'] = $this->processBodyForInputForm($inputForm);
        }

        return $result;
    }

    /**
     * @param array $outputForm
     * @return array|false
     */
    private function processOutputForm($outputForm)
    {
        $result = [];

        $data = $outputForm['data'] ?? ['type' => 'string', 'example' => 'OK'];
        $errors = $outputForm['errors'] ?? [];

        $result['200'] = [
            'description' => [
                'en' => 'Success',
                'ru' => 'Успешный ответ'
            ],
            'content' => [
                'application/json' => [
                    'schema' => [
                        'type' => 'object',
                        'properties' => [
                            'success' => [
                                'type' => 'boolean',
                                'example' => true,
                            ],
                            'data' => $data,
                        ],
                    ],
                ],
            ],
        ];

        foreach ($errors as $error) {
            switch ($error) {
                case 400:
                    $result['400'] = ['$ref' => '#/components/responses/BadRequestError'];
                    break;
                case 401:
                    $result['401'] = ['$ref' => '#/components/responses/UnauthorizedError'];
                    break;
                case 403:
                    $result['403'] = ['$ref' => '#/components/responses/ForbiddenError'];
                    break;
                case 404:
                    $result['404'] = ['$ref' => '#/components/responses/NotFoundError'];
                    break;
            }
        }

        return $result;
    }

    /**
     * @param array $inputForm
     * @return array
     */
    private function processBodyForInputForm($inputForm)
    {
        $result = [];
        if (array_key_exists('bodyDescription', $inputForm)) {
            $result['description'] = $inputForm['bodyDescription'];
        }
        $result['required'] = true;

        $schema = $inputForm['body'];
        $result['content'] = [
            'application/json' => [
                'schema' => $schema,
            ]
        ];
        return $result;
    }

    /**
     * @param array $apiQueryData
     * @return bool
     */
    private function validateApiQueryData($apiQueryData)
    {
        $keysRequired = ['group', 'path', 'controller', 'action', 'method', 'swaggerFilePath'];
        $realKeys = array_keys($apiQueryData);
        $diff = array_diff($keysRequired, $realKeys);
        if (!empty($diff)) {
            $this->addErrorRow(
                'API query data is wrong. It has to have follow fields: ' . implode(', ', $diff)
            );
            return false;
        }

        return true;
    }

    /**
     * @param array $swaggerDoc
     * @param string $lang
     * @return array
     */
    private function applyLang($swaggerDoc, $lang)
    {
        $result = [];

        $rec = function($baseArr, &$arr) use($lang, &$rec) {
            foreach ($baseArr as $key => $value) {
                if ($key === 'description' || $key === 'summary') {
                    if (is_array($value) && array_key_exists($lang, $value)) {
                        $arr[$key] = $value[$lang];
                    } elseif (is_array($value) && array_key_exists('en', $value)) {
                        $arr[$key] = $value['en'];
                    } else {
                        $arr[$key] = $value;
                    }
                } elseif (is_array($value)) {
                    $temp = [];
                    $rec($value, $temp);
                    $arr[$key] = $temp;
                } else {
                    $arr[$key] = $value;
                }
            }
        };
        $rec($swaggerDoc, $result);
        return $result;
    }

    /**
     * @param string $path
     * @return array|false
     */
    private function loadForms($path)
    {
        if (!file_exists($path)) {
            $this->addErrorRow("Validation forms file '{$path}' doesn't exist");
            return false;
        }

        $result = json_decode(file_get_contents($path), true);
        $error = json_last_error();
        if ($error != JSON_ERROR_NONE) {
            $this->addErrorRow('Json parsing problem: ' . json_last_error_msg());
            return false;
        }

        if (empty($result)) {
            $this->addErrorRow("Validation forms in '{$path}' are empty");
            return false;
        }

        return $result;
    }

    /**
     * @return array|false
     */
    private function loadFormsMap()
    {
        $formsMapPath = ValidationFormHelper::getMapFilePath();
        if (!file_exists($formsMapPath)) {
            $this->addErrorRow("Validation forms map file '{$formsMapPath}' doesn't exist");
            return false;
        }

        $map = json_decode(file_get_contents($formsMapPath), true);
        $error = json_last_error();
        if ($error != JSON_ERROR_NONE) {
            $this->addErrorRow('Json parsing problem: ' . json_last_error_msg());
            return false;
        }

        if (empty($map)) {
            $this->addErrorRow('Validation forms map is empty');
            return false;
        }

        return $map;
    }
}
