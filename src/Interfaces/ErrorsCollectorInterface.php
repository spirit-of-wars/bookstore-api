<?php

namespace App\Interfaces;

interface ErrorsCollectorInterface
{
    /**
     * @param $error
     * @return mixed
     */
    public function addError($error): void;

    /**
     * @return bool
     */
    public function hasErrors(): bool;

    /**
     * @return array
     */
    public function getErrors(): array;
}
