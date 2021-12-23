<?php

namespace App\Util\ConsoleCommandExecutor\ValidationForm;

use App\Enum\ApiMethodEnum;
use App\Helper\DocHelper;
use App\Util\Task\Task;
use ReflectionClass;
use ReflectionException;
use ReflectionMethod;

/**
 * Class RenewControllerUtilTask
 * @package App\Util\ConsoleCommandExecutor\ValidationForm
 */
class RenewControllerUtilTask extends Task
{
    const ROUTE_KEY = 'Route';

    /** @var string */
    private $controllerName;

    /** @var ReflectionClass */
    private $controllerReflection;

    /** @var string */
    private $validationFormsGroupName;

    /** @var string */
    private $utilDirectoryPath;

    /**
     * RenewControllerUtilTask constructor.
     * @param string $controllerName
     */
    public function __construct($controllerName)
    {
        parent::__construct();
        $this->addTitle(
            "* Task: Renew validations form files in util directory for '<fg=magenta>{$controllerName}</>'"
        );

        $this->controllerName = $controllerName;
    }

    protected function run()
    {
        if (!$this->controllerName) {
            $this->addErrorRow('Controller name is not defined');
            return;
        }

        $name = $this->defineControllerName($this->controllerName);
        if (!class_exists($name)) {
            $this->addErrorRow("Controller '{$this->controllerName}' doesn't exist");
            return;
        }

        try {
            $this->controllerReflection = new ReflectionClass($name);
        } catch (ReflectionException $exception) {
            $this->addErrorRow($exception->getMessage());
            return;
        }

        if (!$this->defineUtilDirectory()) {
            return;
        }

        $actionsMap = $this->getActionsMap();
        if (!$actionsMap) {
            return;
        }

        foreach ($actionsMap as $actionName => $actionData) {
            $this->processAction($actionName, $actionData);
        }
    }

    /**
     * @param string $actionName
     * @param array $actionData
     */
    private function processAction($actionName, $actionData)
    {
        foreach ($actionData['methods'] as $method) {
            $path = $this->getActionFileName($actionName, $method);
            $msg = "File '{$path}': ";
            if (file_exists($path)) {
                $this->addTextRow($msg . 'already exists');
                return;
            }

            $this->createFile($path, $method, $actionName, $actionData);
            $this->addTextRow($msg . 'has created');
        }
    }

    /**
     * @param string $path
     * @param string $method
     * @param string $actionName
     * @param array $actionData
     */
    private function createFile($path, $method, $actionName, $actionData)
    {
        $name = $this->controllerReflection->name;
        $text = 'MetaData:' . PHP_EOL
            . "  Controller: {$name}" . PHP_EOL
            . "  Action: {$actionName}" . PHP_EOL
            . "  Group: {$this->validationFormsGroupName}" . PHP_EOL
            . "  Method: {$method}" . PHP_EOL
            . "  Path: {$actionData['path']}" . PHP_EOL
            . "  Authentication: {$actionData['auth']}" . PHP_EOL
            . "  SymfonyRouteName: {$actionData['name']}" . PHP_EOL
            . PHP_EOL
            . 'Summary: TODO' . PHP_EOL
            . 'Description: TODO' . PHP_EOL
            . PHP_EOL
            . 'InputForm: TODO' . PHP_EOL
            . PHP_EOL
            . 'OutputForm: TODO' . PHP_EOL
        ;
        file_put_contents($path, $text);
    }

    /**
     * @return array|false
     */
    private function getActionsMap()
    {
        $result = [];
        $methods = $this->controllerReflection->getMethods(ReflectionMethod::IS_PUBLIC);

        if ($this->controllerReflection->hasMethod('getPermissions')) {
            $authMapMethod = $this->controllerReflection->getMethod('getPermissions');
            $authMap = $authMapMethod->invoke(null);
            $methodsWithAuthentication = array_keys($authMap);
        } else {
            $methodsWithAuthentication = [];
        }

        $doc = $this->controllerReflection->getDocComment();
        $docArray = DocHelper::parseDocComment($doc);
        $routePrefix = $docArray[self::ROUTE_KEY][0] ?? '';

        foreach ($methods as $method) {
            $methodName = $method->name;
            $doc = $method->getDocComment();
            $docArray = DocHelper::parseDocComment($doc);
            if (array_key_exists(self::ROUTE_KEY, $docArray)) {
                $data = $docArray[self::ROUTE_KEY];
                $data['path'] = $routePrefix . $data[0];
                unset($data[0]);
                $apiMethods = $data['methods'] ?? null;
                if (!$apiMethods) {
                    $this->addErrorRow(
                        "Methods for action '$methodName' are undefined."
                        ." At least one method has to be defined"
                    );
                    return false;
                }

                foreach ($apiMethods as $apiMethod) {
                    if (!ApiMethodEnum::validateValue($apiMethod)) {
                        $this->addErrorRow("Wrong method '{$apiMethod}' for action '{$methodName}'");
                        return false;
                    }
                }
                $data['methods'] = $apiMethods;

                $data['auth'] = in_array($methodName, $methodsWithAuthentication)
                    ? 'required'
                    : 'free';

                $result[$methodName] = $data;
            }
        }

        return $result;
    }

    /**
     * @param string $actionName
     * @param string $method
     * @return string
     */
    private function getActionFileName($actionName, $method)
    {
        return $this->utilDirectoryPath . '/' . $method . '_' . $actionName . '.yaml';
    }

    /**
     * @return bool
     */
    private function defineUtilDirectory()
    {
        try {
            $this->validationFormsGroupName = $this->controllerReflection
                ->getMethod('getValidationFormsGroupName')
                ->invoke(null);
        } catch (ReflectionException $exception) {
            $this->addErrorRow($exception->getMessage());
            return false;
        }

        $this->utilDirectoryPath = ValidationFormHelper::getUtilPath()
            . '/' . $this->validationFormsGroupName;
        if (!file_exists($this->utilDirectoryPath)) {
            mkdir($this->utilDirectoryPath, 0777, true);
        }

        return true;
    }

    /**
     * @param string $name
     * @return string
     */
    private function defineControllerName($name)
    {
        if (strpos($name, '\\') !== false) {
            return $name;
        }

        return $this->getDefaultControllerNamespace() . '\\' . $name;
    }

    /**
     * @return string
     */
    private function getDefaultControllerNamespace()
    {
        return 'App\Controller';
    }
}
