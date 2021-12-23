<?php

namespace App\Util\Common;

/**
 * Trait ErrorsCollectorTrait
 * @package App\Util\Authentication
 */
trait ErrorsCollectorTrait
{
    private array $errors = [];

    /**
     * @param $error
     */
    public function addError($error) : void
    {
        $this->errors[] = $error;
    }

    /**
     * @return bool
     */
    public function hasErrors(): bool
    {
        return !empty($this->errors);
    }

    /**
     * @return array
     */
    public function getErrors(): array
    {
        return $this->errors;
    }
}
