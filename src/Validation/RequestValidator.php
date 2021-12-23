<?php

namespace App\Validation;

use App\Constants;
use App\Util\Common\ErrorsCollectorTrait;
use App\Util\Undefined;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Validation;

/**
 * Class RequestValidator
 * @package App\Util\Request
 */
class RequestValidator
{
    use ErrorsCollectorTrait;

    /** @var Request */
    private $request;

    /**
     * @param Request $request
     */
    public function validateRequest(Request $request)
    {
        $this->request = $request;

        $validationsMap = new ValidationFormsMap();
        $inputForm = $validationsMap->getInputFormByRequest($request);
        $parameters = $inputForm['parameters'] ?? [];
        $body = $inputForm['body'] ?? [];

        $params = [];
        foreach ($parameters as $parameter) {
            if ($this->validateParameter($parameter)) {
                $params[$parameter['name']] = $request->get($parameter['name']);
            }
        }

        $out = $this->validateBody($body);

        if (!$this->hasErrors()) {
            $params = array_merge($params, $out);
            $request->setValidated($params);
        }
    }

    /**
     * @return JsonResponse
     */
    public function getErrorResponse() : JsonResponse
    {
        return new JsonResponse([
            'success' => false,
            'errorCode' => Response::HTTP_BAD_REQUEST,
            'errorDetails' => $this->getErrors(),
        ], Response::HTTP_BAD_REQUEST);
    }

    /**
     * @param array $parameter
     * @return bool|void
     */
    private function validateParameter($parameter)
    {
        if (!array_key_exists('name', $parameter)
            || !array_key_exists('in', $parameter)
            || !array_key_exists('type', $parameter)
        ) {
            //TODO залогировать, что валидационная форма некорректрая
            return false;
        }

        $undefined = new Undefined();
        switch ($parameter['in']) {
            case 'path':
                //TODO проверка, что это именно path
                $parameterFromRequest = $this->request->get($parameter['name'], $undefined);
                break;
            case 'query':
                $parameterFromRequest = $this->request->query->get($parameter['name'], $undefined);
                break;
            case 'header':
                $parameterFromRequest = $this->request->headers->get($parameter['name'], $undefined);
                break;
            case 'cookie':
                $parameterFromRequest = $this->request->cookies->get($parameter['name'], $undefined);
                break;
            default: $parameterFromRequest = null;
        }

        if ($parameterFromRequest === $undefined) {
            $required = $parameter['required'] ?? false;
            if ($required) {
                $this->addError([
                    'code' => Constants::REQUEST_ERROR_PARAMETER_REQUIRED,
                    'parameter' => $parameter['name'],
                    'message' => 'Параметр должен быть инициализирован',
                ]);
            }

            return false;
        }

        if ($parameterFromRequest === null) {
            $nullable = $parameter['nullable'] ?? true;
            if (!$nullable) {
                $this->addError([
                    'code' => Constants::REQUEST_ERROR_PARAMETER_REQUIRED,
                    'parameter' => $parameter['name'],
                    'message' => 'Параметр не может иметь значение null',
                ]);
                return false;
            }

            return true;
        }

        return $this->validateProperty(
            $parameterFromRequest,
            $parameter,
            $parameter['name']
        );
    }

    /**
     * @param array $body
     * @return array
     */
    private function validateBody($body)
    {
        if (empty($body)) {
            return [];
        }

        $bodyFromRequest = $this->request->request->all();
        $this->validateObject($bodyFromRequest, $body, $out);
        return $out;
    }

    /**
     * @param array $objectFromRequest
     * @param array $objectDefinition
     * @param array $out
     */
    private function validateObject($objectFromRequest, $objectDefinition, &$out = [])
    {
        foreach ($objectDefinition['properties'] as $propertyName => $propertyDefinition) {
            $propertyFromRequest = array_key_exists($propertyName, $objectFromRequest)
                ? $objectFromRequest[$propertyName]
                : new Undefined();

            $required = $propertyDefinition['required'] ?? false;
            if ($propertyFromRequest instanceof Undefined) {
                if ($required) {
                    $this->addError([
                        'code' => Constants::REQUEST_ERROR_PARAMETER_REQUIRED,
                        'parameter' => $propertyName,
                        'message' => 'Параметр должен быть инициализирован',
                    ]);
                }
                continue;
            }

            $nullable = $propertyDefinition['nullable'] ?? true;
            if ($propertyFromRequest === null) {
                if (!$nullable) {
                    $this->addError([
                        'code' => Constants::REQUEST_ERROR_PARAMETER_REQUIRED,
                        'parameter' => $propertyName,
                        'message' => 'Параметр не может иметь значение null',
                    ]);
                } else {
                    $out[$propertyName] = null;
                }
                continue;
            }

            if ($propertyDefinition['type'] == 'object') {
                if (!is_array($propertyFromRequest)) {
                    $this->addError([
                        'code' => Constants::REQUEST_ERROR_PARAMETER_WRONG_TYPE,
                        'parameter' => $propertyName,
                        'message' => 'Значение параметра имеет неверный тип. Ожидаемый тип: object.',
                    ]);
                    continue;
                }

                $this->validateObject($propertyFromRequest, $propertyDefinition, $propertyOut);
                $out[$propertyName] = $propertyOut;
            } else {
                if ($this->validateProperty($propertyFromRequest, $propertyDefinition, $propertyName)) {
                    $out[$propertyName] = $propertyFromRequest;
                }
            }
        }
    }

    /**
     * @param mixed $propertyFromRequest
     * @param mixed $propertyDefinition
     * @param string|int $propertyName
     * @return bool
     */
    private function validateProperty($propertyFromRequest, $propertyDefinition, $propertyName)
    {
        if ($propertyDefinition['type'] == 'array') {
            if (!$this->validateItemsType($propertyFromRequest, $propertyDefinition, $propertyName)) {
                return false;
            }
        } else {
            if (!$this->validateType($propertyFromRequest, $propertyDefinition)) {
                $this->addError([
                    'code' => Constants::REQUEST_ERROR_PARAMETER_WRONG_TYPE,
                    'parameter' => $propertyName,
                    'message' => 'Значение параметра имеет неверный тип. Ожидаемый тип: ' . $propertyDefinition['type'] . '.',
                ]);
                return false;
            }
        }

        if (array_key_exists('constraints', $propertyDefinition)) {
            return $this->validateValueWithConstraints(
                $propertyFromRequest,
                $propertyDefinition['constraints'],
                $propertyName
            );
        }

        return true;
    }

    /**
     * @param $array
     * @param $definition
     * @param string $arrayName
     * @return bool
     */
    private function validateItemsType($array, $definition, $arrayName)
    {
        if (!is_array($array)) {
            $array = [$array];
        }

        foreach ($array as $i => $item) {
            if (!$this->validateProperty($item, $definition['items'], $arrayName . '[' . $i . ']')) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param mixed $value
     * @param $definition
     * @return bool
     */
    private function validateType($value, $definition)
    {
        switch ($definition['type']) {
            case 'integer':
                return (filter_var($value, FILTER_VALIDATE_INT) !== false);
            case 'float':
                return (filter_var($value, FILTER_VALIDATE_FLOAT) !== false);
            case 'boolean':
                return is_bool($value);
            case 'string':
                return (is_numeric($value) || is_string($value));
        }

        return true;
    }

    /**
     * @param string $property
     * @param array $constraints
     * @param string $propertyName
     * @return bool
     */
    private function validateValueWithConstraints($property, $constraints, $propertyName)
    {
        $constraintArray = [];
        foreach ($constraints as $name => $params) {
            $constraintClass = ConstraintCore::getConstraintClass($name);
            if ($constraintClass) {
                $constraintArray[] = $params
                    ? new $constraintClass($params)
                    : new $constraintClass();
            }
        }
        if (empty($constraintArray)) {

            return true;
        }

        $validator = Validation::createValidator();
        $violations = $validator->validate($property, $constraintArray);
        if (0 !== count($violations)) {
            $violation = $violations[0];
            $this->addError([
                'code' => Constants::REQUEST_ERROR_PARAMETER_CONSTRAINT,
                'parameter' => $propertyName,
                'message' => $violation->getMessage(),
            ]);
            return false;
        }

        return true;
    }
}
