<?php

namespace App\EventListener;

use App\Mif;
use App\MifTools\MifServiceProvider;
use Psr\Container\ContainerInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Contracts\Service\ServiceSubscriberInterface;

/**
 * Class KernelRequestListener
 * @package App\EventListener
 */
class KernelRequestListener implements ServiceSubscriberInterface
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
     * @return array
     */
    public static function getSubscribedServices()
    {
        return [
            'serviceProvider' => MifServiceProvider::class,
        ];
    }

    /**
     * @param RequestEvent $event
     */
    public function onKernelRequest(RequestEvent $event)
    {
        Mif::setServiceProvider($this->container->get('serviceProvider'));
    }
}
