<?php

namespace App\Service\Entity;

use App\Entity\VirtualPage;
use App\Repository\VirtualPageRepository;
use App\Request;
use App\Service\Serializer\EntitySerializer;

/**
 * Class VirtualPageService
 * @package App\Service\VirtualPage
 *
 * @method VirtualPage getEntity($id)
 * @method VirtualPage createEntity($properties)
 * @property-read EntitySerializer EntitySerializer
 */
class VirtualPageService extends EntityService
{
    /**
     * @return array|string[]
     */
    protected static function subscribedServicesMap()
    {
        return array_merge(parent::subscribedServicesMap(), [
            'EntitySerializer' => EntitySerializer::class,
        ]);
    }

    /**
     * @return string
     */
    protected function getEntityClassName()
    {
        return VirtualPage::class;
    }

    /**
     * @param Request $request
     * @param array $aliases
     * @return VirtualPage
     */
    public function createEntityFromRequest($request, $aliases = [])
    {
        $properties = $this->extractPropertiesFromRequest($request, $aliases);

        if (array_key_exists('parentVirtualPage', $properties)) {
            $parent = $this->getEntity($properties['parentVirtualPage']);
            if ($parent) {
                $properties['level'] = $parent->getLevel() + 1;
            }
        }

        if (!array_key_exists('level', $properties)) {
            $properties['level'] = 0;
        }

        return $this->createEntity($properties);
    }

    /**
     * @return array
     */
    public function getMenu()
    {
        /** @var VirtualPageRepository $repository */
        $repository = $this->getRepository();
        /** @var VirtualPage[] $virtualPages  */
        $virtualPages = $repository->getVirtualPagesIsMenu();

        $virtualPagesArray = [];
        $serializer = $this->EntitySerializer;

        foreach ($virtualPages as $virtualPage) {
            $virtualPagesArray[] = array_merge(
                $serializer->serialize($virtualPage),
                ['subMenu' => $serializer->serializeList($virtualPage->getSubVirtualPages())]
            );
        }

        return $virtualPagesArray;
    }
}
