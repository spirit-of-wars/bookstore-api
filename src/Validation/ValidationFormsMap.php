<?php

namespace App\Validation;

use App\Exception\BadRequestException;
use App\Mif;
use App\Request;
use App\Util\ConsoleCommandExecutor\ValidationForm\ValidationFormHelper as validationFormHelper;

/**
 * Class ValidationFormsMap
 * @package App\Validation
 */
class ValidationFormsMap
{
    /** @var array */
    private static $map;

    /** @var array */
    private static $inputForms;

    /**
     * @param Request $request
     * @return array
     */
    public function getInputFormByRequest(Request $request) : array
    {
        $action = $request->get('_controller');
        $requestMethod = strtolower($request->getMethod());
        $key = $action .'::'. $requestMethod;

        $validationFormKey = $this->getInputFormKey($request, $key);
        return $this->getInputForm($validationFormKey);
    }

    /**
     * @param Request $request
     * @param string $key
     * @return string
     */
    private function getInputFormKey(Request $request, string $key) : string
    {
        $map = $this->getMap();

        if (isset($map[$key]['mapping']['by'])) {
            $mapFactor = $map[$key]['mapping']['by'];
            $type = $request->get($mapFactor);
            return $key . '::' . $type;
        }

        return $key;
    }

    /**
     * @param string $validationFormKey
     * @return array
     */
    private function getInputForm(string $validationFormKey) : array
    {
        $form = $this->getValidationForm($validationFormKey);
        return $form['inputForm'] ?? [];
    }

    /**
     * @param string $validationFormKey
     * @return array
     * @throws BadRequestException
     */
    private function getValidationForm(string $validationFormKey) : array
    {
        if (!isset(self::$inputForms)) {
            $this->getMap();
        }

        if (!isset(self::$inputForms[$validationFormKey])) {
            throw new BadRequestException('Validation form not found with key : ' . $validationFormKey);
        }

        if (is_string(self::$inputForms[$validationFormKey])) {
            $path = Mif::getProjectDir() . self::$inputForms[$validationFormKey];
            if (file_exists($path)) {
                $form = json_decode(file_get_contents($path), true) ?? [];
                self::$inputForms[$validationFormKey] = $form;
            } else {
                self::$inputForms[$validationFormKey] = [];
            }
        }

        return self::$inputForms[$validationFormKey];
    }

    /**
     * @return array
     */
    private function getMap() : array
    {
        if (!isset(self::$map)) {
            $mapFile = validationFormHelper::getMapFilePath();
            if (file_exists($mapFile)) {
                self::$map = json_decode(file_get_contents($mapFile), true) ?? [];
                self::$inputForms = [];
                foreach (self::$map as $key => $value) {
                    if (array_key_exists('mapping', $value)) {
                        foreach ($value['mapping']['map'] as $type => $item) {
                            self::$inputForms[$key . '::' . $type] = $item['validationFilePath'];
                        }
                    } else {
                        self::$inputForms[$key] = $value['validationFilePath'];
                    }
                }
            } else {
                self::$map = [];
                self::$inputForms = [];
            }
        }

        return self::$map;
    }
}
