<?php

namespace App\Validation\Constraint;

/**
 * Class Slug
 * @package App\Validation\Constraint
 */
class Slug extends AbstractConstraint
{
    const NAME = 'Slug';

    public string $message = 'Slug не может содержать русских символов, пробелов и не может быть меньше 1 символа.';

    /**
     * @return string
     */
    public static function getName()
    {
        return self::NAME;
    }

    /**
     * @return string
     */
    public function getDocumentationComment()
    {
        return 'Может состоять из латинских символов, цифр, знаков подчеркивания. Должен иметь как минимум один символ.';
    }
}
