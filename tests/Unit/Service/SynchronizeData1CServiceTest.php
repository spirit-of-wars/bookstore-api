<?php

namespace App\Tests\Unit\Service;

use App\Service\Entity\CommonProductService;
use App\Service\Entity\Data1CService;
use App\Service\SynchronizeData1CService;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class SynchronizeData1CServiceTest extends KernelTestCase
{
    const ARR_DATA = [
        [
            'Product'         => 'test Book 2',
            'isbn'            => '123',
            'group1c'         => 22,
            'id1c'            => 00000000454,
            'itf14'           => 64600,
            'vat'             => '10',
            'quantityPerPack' => 6,
            'rrPrice'         => 432,
            'weight'          => 464,
            'height'          => 484,
            'width'           => 486,
            'length'          => 310,
            'ageLimit'        => '12'
        ],
        [
            'Product'         => 'Test Book 1',
            'isbn'            => '122',
            'group1c'         => 23,
            'id1c'            => 00000000455,
            'itf14'           => 64600503,
            'vat'             => '11',
            'quantityPerPack' => 7,
            'rrPrice'         => 433,
            'weight'          => 465,
            'height'          => 485,
            'width'           => 487,
            'length'          => 311,
            'ageLimit'        => '13'
        ],
        [
            'Product'         => 'test Book 1',
            'isbn'            => '123',
            'group1c'         => 24,
            'id1c'            => 00000000455,
            'itf14'           => 64612424,
            'vat'             => '12',
            'quantityPerPack' => 7,
            'rrPrice'         => 500,
            'weight'          => 464,
            'height'          => 484,
            'width'           => 486,
            'length'          => 310,
            'ageLimit'        => '12'
        ],
        [
            'Product'         => 'test Book 3',
            'isbn'            => '124',
            'group1c'         => 25,
            'id1c'            => 00000000455,
            'itf14'           => 646005031,
            'vat'             => '12',
            'quantityPerPack' => 7,
            'rrPrice'         => 500,
            'weight'          => 464,
            'height'          => 484,
            'width'           => 486,
            'length'          => 310,
            'ageLimit'        => '12'
        ],
    ];

    private Data1CService $data1CService;
    private SynchronizeData1CService $synchronizeData1CService;
    private CommonProductService $commonProductService;
    private EntityManager $em;
    private string $arrDataJson;

    public function setUp() : void
    {
        $kernel = self::bootKernel();
        $serviceContainer = $kernel->getContainer()->get('service_container');
        $this->data1CService = new Data1CService($serviceContainer);
        $this->commonProductService = new CommonProductService($serviceContainer);
        $this->em = $kernel->getContainer()->get('doctrine')->getManager();
        $this->synchronizeData1CService = new SynchronizeData1CService($this->commonProductService, $this->data1CService);
        $this->arrDataJson = json_encode(self::ARR_DATA);
    }

    public function testSaveData1C() : void
    {
         $result = $this->synchronizeData1CService->saveData1C($this->arrDataJson);
         $this->assertNull($result);
    }
}
