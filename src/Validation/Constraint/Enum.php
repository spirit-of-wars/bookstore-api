<?php

namespace App\Validation\Constraint;

/**
 * Class Enum
 * @package App\Validation\Constraint
 */
class Enum extends AbstractConstraint
{
    const NAME = 'Enum';

    public $enumName;
    public $enumList;
    public $message = 'Значение параметра должно соответствовать одному из значений перечисления: {{ enum }}. Переданное значение: {{ value }}.';

    /**
     * @return string
     */
    public static function getName()
    {
        return self::NAME;
    }

    /**
     * Enum constructor.
     * Argument variants:
     * 1. new Enum("EnumClassName")
     * 2. new Enum("item0", "item1", "item2" ...)
     */
    public function __construct(...$args)
    {
        $count = count($args);
        if ($count == 1) {
            $options = ['enumName' => $args[0]];
        } elseif ($count > 1) {
            $options = ['enumList' => $args];
        } else {
            $options = [];
        }

        parent::__construct($options);
    }

    /**
     * @return string
     */
    public function getDocumentationComment()
    {
        return 'Значение должно соответствовать перечислению (см. ниже перечень).';
    }
}
