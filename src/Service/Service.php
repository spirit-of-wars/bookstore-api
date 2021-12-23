<?php

namespace App\Service;

use App\Kernel;
use App\Mif;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectManager;
use Psr\Container\ContainerInterface;
use Symfony\Contracts\Service\ServiceSubscriberInterface;

/**
 * Class Service
 * @package App\Service
 */
class Service implements ServiceSubscriberInterface
{
    protected ContainerInterface $container;

    /**
     * Service constructor.
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @param string $name
     * @return object|null
     */
    public function __get($name)
    {
        if (array_key_exists($name, static::subscribedServicesMap())) {
            return $this->container->get($name);
        }

        return null;
    }

    /**
     * @return array
     */
    protected static function subscribedServicesMap()
    {
        return [];
    }

    /**
     * @return array
     */
    public static function getSubscribedServices()
    {
        return array_merge([
            'doctrine' => ManagerRegistry::class,
        ], static::subscribedServicesMap());
    }

    /**
     * @return string
     */
    protected function getProjectDir(): string
    {
        return $this->getApp()->getProjectDir();
    }

    /**
     * @return Kernel
     */
    protected function getApp(): Kernel
    {
        return Mif::$app;
    }

    /**
     * @return ManagerRegistry
     */
    protected function getDoctrine(): ManagerRegistry
    {
        return $this->container->get('doctrine');
    }

    /**
     * @param string $name
     * @return ObjectManager
     */
    protected function getEntityManager($name = null): ObjectManager
    {
        return $this->getDoctrine()->getManager($name);
    }
}
