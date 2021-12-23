<?php

namespace App\Service\Entity;

use App\Entity\Resource;
use App\Entity\VirtualPageResource\Banner;
use App\EntitySupport\Behavior\ResourceGetterBehavior;
use App\EntitySupport\Common\BaseEntity;
use App\Enum\BannerType;
use App\Exception\BadRequestException;
use App\Exception\EntityNotFoundException;
use App\Mif;
use App\Repository\VirtualPageResource\BannerRepository;
use App\Request;
use App\Service\File\FileService;
use App\Enum\ResourceAssigmentEnum;

/**
 * Class BannerService
 * @package App\Service\Entity
 *
 * @method BannerRepository getRepository()
 * @property-read FileService FileService
 */
class BannerService extends EntityService
{
    /**
     * @return array|string[]
     */
    protected static function subscribedServicesMap()
    {
        return array_merge(parent::subscribedServicesMap(), [
            'FileService' => FileService::class,
        ]);
    }
    /**
     * @return string
     */
    protected function getEntityClassName() : string
    {
        return Banner::class;
    }

    /**
     * @param Request $request
     * @param array $aliases
     * @return BaseEntity
     */
    public function createMiniBannerFromRequest($request, $aliases = [])
    {
        $properties = $this->extractPropertiesFromRequest($request, $aliases);
        $properties['type'] = BannerType::MINI_BANNER;

        return $this->createEntity($properties);
    }

    /**
     * @param Request $request
     * @param array $aliases
     * @return BaseEntity
     */
    public function createBigBannerFromRequest($request, $aliases = [])
    {
        $properties = $this->extractPropertiesFromRequest($request, $aliases);
        $properties['type'] = BannerType::BIG_BANNER;

        return $this->createEntity($properties);
    }

    /**
     * @param Request $request
     * @return BaseEntity
     */
    public function updateBig($request)
    {
        /** @var Banner $entity */
        $entity = $this->getEntity($request->get('id'));
        if (!$entity) {
            throw new EntityNotFoundException('Объект не найден');
        }

        $resourceService = Mif::getServiceProvider()->ResourceService;

        $properties = $request->all();
        $resourceService->defineResourcesByAssignments(
            $entity,
            [
                ResourceAssigmentEnum::IMAGE_320 => 'resources',
                ResourceAssigmentEnum::IMAGE_480 => 'resources',
                ResourceAssigmentEnum::IMAGE_960 => 'resources',
            ],
            $properties
        );

        $this->updateEntity($entity, $properties);
        return $entity;
    }

    /**
     * @param Request $request
     * @return BaseEntity
     */
    public function updateMini($request)
    {
        /** @var Banner $entity */
        $entity = $this->getEntity($request->get('id'));
        if (!$entity) {
            throw new EntityNotFoundException('Объект не найден');
        }

        $resourceService = Mif::getServiceProvider()->ResourceService;

        $properties = $request->all();
        $resourceService->defineResourcesByAssignments(
            $entity,
            [
                ResourceAssigmentEnum::MINI_BANNER_IMAGE => 'resources',
            ],
            $properties
        );

        $this->updateEntity($entity, $properties);
        return $entity;
    }

    /**
     * @param array $bannerIds
     * @return array
     */
    public function findBannersByIds(array $bannerIds) : array
    {
        return $this->getRepository()->findBannersByIds($bannerIds);
    }
}
