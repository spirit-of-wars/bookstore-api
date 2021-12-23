<?php

namespace App\Validation\TranslatedConstraint;

use App\Interfaces\ConstraintInterface;
use App\Validation\ConstraintCore;
use Symfony\Component\Validator\Constraints\LengthValidator;

/**
 * Class Length
 * @package App\Validation\TranslatedConstraint
 */
class Length extends \Symfony\Component\Validator\Constraints\Length implements ConstraintInterface
{
    public $maxMessage = 'Значение слишком длинное. Допустимо {{ limit }} символов или меньше.|Значение слишком длинное. Допустимо {{ limit }} символов или меньше.';
    public $minMessage = 'Значение слишком короткое. Допустимо {{ limit }} символов или больше.|Значение слишком короткое. Допустимо {{ limit }} символов или больше.';
    public $exactMessage = 'Значение должно состоять ровно из {{ limit }} символов.|Значение должно состоять ровно из {{ limit }} символов.';
    public $charsetMessage = 'Значение не соответствет требуему набору символов {{ charset }}.';

    public static function register()
    {
        ConstraintCore::registerConstraint('Length', static::class);
    }

    /**
     * @return string
     */
    public function validatedBy()
    {
        return LengthValidator::class;
    }

    /**
     * @return string
     */
    public function getDocumentationComment()
    {
        if ($this->min == $this->max) {
            return "Допустимое количество символов: ровно {$this->max}.";
        }

        if ($this->min && $this->max) {
            return "Допустимое количество символов: от {$this->min} до {$this->max}.";
        }

        if ($this->min) {
            return "Допустимое количество символов: {$this->min} или больше.";
        }

        if ($this->max) {
            return "Допустимое количество символов: {$this->max} или меньше.";
        }

        return 'Ограничение сформулировано некорректно. Обратитесь к разработчикам.';
    }
}
