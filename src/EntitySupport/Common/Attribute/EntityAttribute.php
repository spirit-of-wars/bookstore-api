<?php

namespace App\EntitySupport\Common\Attribute;

use App\EntitySupport\Common\EntityConstants;
use App\EntitySupport\Common\EntityProperty;
use App\EntitySupport\OrmType\DictionaryType;
use App\EntitySupport\OrmType\ListType;
use App\Exception\AttributeValidationException;
use App\Validation\ConstraintCore;
use DateTime;
use Symfony\Component\Validator\Validation;

/**
 * Class EntityAttribute
 * @package App\Entity\Common\Attribute
 */
class EntityAttribute extends EntityProperty
{
    /** @var array */
    private $ormDefinition;

    /** @var array */
    private $constraints;

    /** @var array */
    private $fieldData;

    /**
     * EntityAttribute constructor.
     * @param string $name
     * @param array $definition
     */
    public function __construct($name, $definition)
    {
        parent::__construct($name);
        $this->constraints = [];
        $this->fieldData = [];

        foreach ($definition as $key => $value) {
            if ($key == EntityConstants::ATTRIBUTE_ORM_DATA) {
                $this->ormDefinition = $definition[$key];
                continue;
            }

            if ($key == EntityConstants::ATTRIBUTE_APP_FIELD) {
                $this->fieldData = $definition[$key];
                continue;
            }

            $regExp = '/^' . addcslashes(EntityConstants::ATTRIBUTE_APP_CONSTRAINT, '\\') . ':(.+)$/';
            if (preg_match($regExp, $key, $matches)) {
                if (is_array($value) && array_key_exists(0, $value) && count($value) == 1) {
                    $value = $value[0];
                }

                $this->constraints[$matches[1]] = $value;
                continue;
            }
        }
    }

    /**
     * @return bool
     */
    public function isRelation()
    {
        return false;
    }

    /**
     * @param string $attributeName
     * @param array $definition
     * @return EntityAttribute
     */
    public static function create($attributeName, $definition)
    {
        switch ($definition[EntityConstants::ATTRIBUTE_ORM_DATA]['type']) {
            case ListType::NAME:
                return new EntityAttributeList($attributeName, $definition);
            case DictionaryType::NAME:
                return new EntityAttributeDictionary($attributeName, $definition);
            default:
                return new self($attributeName, $definition);
        }
    }

    /**
     * @param mixed $value
     * @return bool
     * @throws AttributeValidationException
     */
    public function validate($value)
    {
        $constraints = [];
        foreach ($this->constraints as $name => $params) {
            $constraintClass = ConstraintCore::getConstraintClass($name);
            if ($constraintClass) {
                $constraint = $params
                    ? new $constraintClass($params)
                    : new $constraintClass();
                $constraints[] = $constraint;
            }
        }

        if (empty($constraints)) {
            return true;
        }

        $validator = Validation::createValidator();
        $violations = $validator->validate($value, $constraints);
        if (0 !== count($violations)) {
            $violation = $violations[0];
            throw new AttributeValidationException($violation->getMessage());
        }

        return true;
    }

    /**
     * @return string|null
     */
    public function getDocumentationComment()
    {
        return null;
    }

    /**
     * @param $value
     * @return mixed
     */
    public function preprocessValue($value)
    {
        if ($this->getType() == 'datetime' && is_string($value)) {
            $value = new DateTime($value);
        }

        return $value;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->ormDefinition['type'];
    }

    /**
     * @param string $type
     * @return bool
     */
    public function typeIs($type)
    {
        return $this->getType() == $type;
    }

    /**
     * @return array
     */
    public function getConstraints()
    {
        return $this->constraints;
    }

    /**
     * @return bool
     */
    public function isRequired()
    {
        $nullable = $this->ormDefinition['nullable'] ?? false;
        return (!$nullable && !array_key_exists('default', $this->ormDefinition));
    }

    /**
     * @return bool
     */
    public function isHiddenForInputForm()
    {
        $inForm = $this->fieldData['inForm'] ?? null;
        if (!$inForm) {
            return false;
        }

        return $inForm == EntityConstants::HIDDEN_FIELD_FOR_FORM
            || $inForm == EntityConstants::HIDDEN_FIELD_FOR_INPUT_FORM;
    }

    /**
     * @return bool
     */
    public function isHiddenForOutputForm()
    {
        $inForm = $this->fieldData['inForm'] ?? null;
        if (!$inForm) {
            return false;
        }

        return $inForm == EntityConstants::HIDDEN_FIELD_FOR_FORM
            || $inForm == EntityConstants::HIDDEN_FIELD_FOR_OUTPUT_FORM;
    }

    /**
     * @param mixed $value
     * @param string $key
     * @return mixed
     */
    public function onActionGetItem($value, $key)
    {
        return $value;
    }

    /**
     * @param mixed $oldValue
     * @param array $arguments
     * @return mixed
     */
    public function onActionAdd($oldValue, $arguments)
    {
        return $oldValue;
    }

    /**
     * @param mixed $oldValue
     * @param array $arguments
     * @return mixed
     */
    public function onActionRemove($oldValue, $arguments)
    {
        return $oldValue;
    }

    /**
     * @return array
     */
    public function getAllowedActions()
    {
        return [
            EntityConstants::ACTION_GET,
            EntityConstants::ACTION_SET
        ];
    }
}
