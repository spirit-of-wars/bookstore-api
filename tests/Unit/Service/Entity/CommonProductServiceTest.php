<?php

namespace App\Tests\Unit\Service\Entity;

use App\Entity\Product;
use App\Service\Entity\CommonProductService;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class CommonProductServiceTest extends KernelTestCase
{
    private CommonProductService $commonProductService;
    private EntityManager $em;

    public function setUp() : void
    {
        $kernel = self::bootKernel();
        $serviceContainer = $kernel->getContainer()->get('service_container');
        $this->commonProductService = new CommonProductService($serviceContainer);
        $this->em = $kernel->getContainer()->get('doctrine')->getManager();
    }

    /**
     * @dataProvider sendDataProvider
     */
    public function testGetWithActiveData1CByFullNameAndIsbn(string $product, string $isbn) :void
    {
        $result = $this->commonProductService->getWithActiveData1CByFullNameAndIsbn($product, $isbn);
        if (mb_strtolower($product) === 'test book 2' && $isbn === '123') {
            $this->assertCount(2, $result);
        } else {
            $this->assertNull($result);
        }
    }

    /**
     * @dataProvider sendDataProvider
     */

    public function testGetProductByName(string $product) : void
    {
        $result = $this->commonProductService->getProductByName($product);
        if (mb_strtolower($product) === 'test book 3' || mb_strtolower($product) === 'test book 2') {
            $this->assertInstanceOf(Product::class, $result);
        } else {
            $this->assertNull($result);
        }
    }

    public function sendDataProvider() : array
    {
        return [
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
                'itf14'           => 646005024,
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
                'itf14'           => 643112424,
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
    }
}
