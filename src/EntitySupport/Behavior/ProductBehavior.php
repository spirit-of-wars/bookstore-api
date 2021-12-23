<?php

namespace App\EntitySupport\Behavior;

use App\Helper\TranslitHelper;
use Exception;

/**
 * Class ProductBehavior
 * @package App\Entity\Behavior
 */
trait ProductBehavior
{
    /**
     * @param string $name
     * @param mixed $value
     */
    protected function afterSetAttribute($name, $value)
    {
        if ($name === 'fullName' && $this->slug === null) {
            $this->setSlugAttribute($value);
        }
    }

    /**
     * @param $value
     * @throws Exception
     */
    private function setSlugAttribute($value)
    {
        $arrayWords = explode(' ', $value);
        if (count($arrayWords) > 5) {
            $value = implode('-', array_slice($arrayWords, 0, 5));
        }

        $value = TranslitHelper::cyrillicTransliter($value, '-');

        $this->actionSet('slug', $value . '-'. bin2hex(random_bytes(4)));
    }
}
