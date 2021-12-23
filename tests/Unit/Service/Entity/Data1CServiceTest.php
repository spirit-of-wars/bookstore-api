<?php

namespace App\Tests\Unit\Service\Entity;

use App\Entity\ProductData\Data1C;
use App\Service\Entity\Data1CService;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class Data1CServiceTest extends KernelTestCase
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
    private EntityManager $em;

    public function setUp() : void
    {
        $kernel = self::bootKernel();
        $serviceContainer = $kernel->getContainer()->get('service_container');
        $this->data1CService = new Data1CService($serviceContainer);
        $this->em = $kernel->getContainer()->get('doctrine')->getManager();
    }

    public function testUpdateData1C() : void
    {
        $arrProduct = $this->em->createQuery(
            'SELECT p, d
                FROM App\Entity\Product p
                LEFT JOIN p.data1C d
                WHERE d.isbn = :isbn and LOWER(p.fullName) = LOWER(:nameProduct) and d.isActive = true'
        )
            ->setParameter('isbn', '123')
            ->setParameter('nameProduct', 'Test Book 2')
            ->getResult();

        $data1C = $arrProduct[0]->getData1C();
        $result = $this->data1CService->updateData1C($data1C[0], self::ARR_DATA[1]);
        $this->assertIsObject($result);
    }

    public function testCreateData1C() : void
    {
        $data1C = $this->em->getRepository(Data1C::class)->createQueryBuilder('d')
            ->andWhere('d.product = :idProduct')
            ->andWhere('d.isActive = true')
            ->setParameter('idProduct', 2)
            ->getQuery()
            ->getOneOrNullResult();

        $results = $this->data1CService->createNextData1C($data1C, self::ARR_DATA[0]);
        foreach ($results as $result) {
            $this->assertInstanceOf(Data1C::class, $result);
        }
    }

    public function testCreateData1CWithNull() : void
    {
        $results = $this->data1CService->createNextData1C(null, self::ARR_DATA[1]);
        foreach ($results as $result) {
            $this->assertInstanceOf(Data1C::class, $result);
        }

    }

}

