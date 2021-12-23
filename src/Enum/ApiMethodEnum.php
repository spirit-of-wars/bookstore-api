<?php

namespace App\Enum;

use App\Enum\Core\Enum;

/**
 * Class ApiMethodEnum
 * @package App\Enum
 */
class ApiMethodEnum extends Enum
{
    const GET = 'GET';
    const POST = 'POST';
    const PUT = 'PUT';
    const PATCH = 'PATCH';
    const DELETE = 'DELETE';
}
