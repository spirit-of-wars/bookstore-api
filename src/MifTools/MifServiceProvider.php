<?php

namespace App\MifTools;

use App\Service\Authentication\AuthenticationOAuth2Service;
use App\Service\Entity\AccessTokenService;
use App\Service\Entity\AuthorService;
use App\Service\Entity\BannerService;
use App\Service\Entity\BannerShelfService;
use App\Service\Entity\BookService;
use App\Service\Entity\ConfirmLinkService;
use App\Service\Entity\Data1CService;
use App\Service\Entity\EssenceService;
use App\Service\Entity\FactoidService;
use App\Service\Entity\Product\CommonService as ProductCommonService;
use App\Service\Entity\ProductShelfService;
use App\Service\Entity\PromoTagService;
use App\Service\Entity\RefreshTokenService;
use App\Service\Entity\ResourceService;
use App\Service\Entity\RoleService;
use App\Service\Entity\SeriesService;
use App\Service\Entity\SocNetworkUserDataService;
use App\Service\Entity\TagService;
use App\Service\Entity\UserService;
use App\Service\Entity\CreativePartnerService;
use App\Service\Entity\VirtualPageService;
use App\Service\Entity\CategoryService;
use App\Service\MailerService;
use App\Service\File\FileService;
use App\Service\Serializer\EntitySerializer;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Contracts\Service\ServiceSubscriberInterface;

/**
 * Class MifContainer
 * @package App\MifTools
 *
 * @property-read LoggerInterface $LoggerInterface
 * @property-read LoggerInterface $Logger
 * @property-read AuthenticationOAuth2Service $AuthenticationOAuth2Service
 * @property-read EntitySerializer $EntitySerializer
 *
 * @property-read ProductCommonService $ProductCommonService
 * @property-read AccessTokenService $AccessTokenService
 * @property-read AuthorService $AuthorService
 * @property-read BannerService $BannerService
 * @property-read BannerShelfService $BannerShelfService
 * @property-read CategoryService $CategoryService
 * @property-read ConfirmLinkService $ConfirmLinkService
 * @property-read CreativePartnerService $CreativePartnerService
 * @property-read Data1CService $Data1CService
 * @property-read FactoidService $FactoidService
 * @property-read ProductShelfService $ProductShelfService
 * @property-read PromoTagService $PromoTagService
 * @property-read RefreshTokenService $RefreshTokenService
 * @property-read RoleService $RoleService
 * @property-read SeriesService $SeriesService
 * @property-read SocNetworkUserDataService $SocNetworkUserDataService
 * @property-read TagService $TagService
 * @property-read UserService $UserService
 * @property-read VirtualPageService $VirtualPageService
 * @property-read ResourceService $ResourceService
 * @property-read MailerService $MailerService
 * @property-read FileService $FileService
 */
class MifServiceProvider implements ServiceSubscriberInterface
{
    /** @var ContainerInterface */
    private ContainerInterface $container;

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
        if (array_key_exists($name, static::getSubscribedServices())) {
            return $this->container->get($name);
        }

        return null;
    }

    /**
     * @return ContainerInterface
     */
    public function getContainer()
    {
        return $this->container;
    }

    /**
     * @return array
     */
    public static function getSubscribedServices()
    {
        return [
            'LoggerInterface' => LoggerInterface::class,
            'Logger' => LoggerInterface::class,
            'AuthenticationOAuth2Service' => AuthenticationOAuth2Service::class,
            'EntitySerializer' => EntitySerializer::class,

            // Entity services
            'ProductCommonService' => ProductCommonService::class,
            'AccessTokenService' => AccessTokenService::class,
            'AuthorService' => AuthorService::class,
            'BannerService' => BannerService::class,
            'BannerShelfService' => BannerShelfService::class,
            'BookService' => BookService::class,
            'CategoryService' => CategoryService::class,
            'ConfirmLinkService' => ConfirmLinkService::class,
            'CreativePartnerService' => CreativePartnerService::class,
            'Data1CService' => Data1CService::class,
            'EssenceService' => EssenceService::class,
            'FactoidService' => FactoidService::class,
            'ProductShelfService' => ProductShelfService::class,
            'PromoTagService' => PromoTagService::class,
            'RefreshTokenService' => RefreshTokenService::class,
            'RoleService' => RoleService::class,
            'SeriesService' => SeriesService::class,
            'SocNetworkUserDataService' => SocNetworkUserDataService::class,
            'TagService' => TagService::class,
            'UserService' => UserService::class,
            'VirtualPageService' => VirtualPageService::class,
            'ResourceService' => ResourceService::class,
            'MailerService' => MailerService::class,
            'FileService' => FileService::class,
        ];
    }
}
