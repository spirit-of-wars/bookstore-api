<?php

namespace App\Exception;

use Exception;

/**
 * Class MifException
 * @package App\Exception
 */
abstract class MifException extends Exception
{
    /**
     * Метод для переопределения у потомков, чтобы, например, логировать проблему
     * Вызовется автоматически
     */
    public function process()
    {
        // pass
    }
}
