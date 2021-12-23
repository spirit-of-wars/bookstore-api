<?php

namespace App\EntitySupport\Common;

/**
 * Class EntityConstants
 * @package App\Entity\Common
 */
class EntityConstants
{
    const ACTION_GET = 'get';
    const ACTION_SET = 'set';
    const ACTION_GET_ITEM = 'getFrom';
    const ACTION_ADD = 'addTo';
    const ACTION_REMOVE = 'removeFrom';

    const HIDDEN_FIELD_FOR_FORM = 'hidden';
    const HIDDEN_FIELD_FOR_INPUT_FORM = 'inputHidden';
    const HIDDEN_FIELD_FOR_OUTPUT_FORM = 'outputHidden';

    const ATTRIBUTE_ORM_DATA = 'ORM\Column';
    const ATTRIBUTE_APP_CONSTRAINT = 'App\Constraint';
    const ATTRIBUTE_APP_FIELD = 'App\Field';
}
