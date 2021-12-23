<?php

namespace App\Validation\ValidationFormMacros;

/**
 * Class EntityList
 * @package App\Validation\ValidationFormMacros
 */
class EntityList extends AbstractMacros
{
    /**
     * @param string $param
     * @return array|null
     */
    public function run($param)
    {
        $entities = preg_split('/\+/', $param);
        foreach ($entities as &$entity) {
            $entity = trim($entity, ' ');
        }
        unset($entity);

        $count = count($entities);
        if ($count == 0) {
            return null;
        }

        $items = ($count == 1)
            ? ['$entity' => $entities[0]]
            : ['$entities' => $entities];

        return [
            'paging' => [
                'pageCount' => 'integer',
                'pageSize' => 'integer',
                'page' => 'integer',
                'itemsCount' => 'integer',
            ],
            'list' => [
                '$type' => 'array',
                '$items' => $items,
            ]
        ];
    }
}
