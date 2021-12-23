<?php

namespace App\Tests\Unit\Service;

use App\Service\AuthApiService;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class AuthApiTest extends KernelTestCase
{
    const ARRAY_DATA_REQUEST = [
        [
            "id"        => 123,
            "Product"   => "Test book 1",
            "rrc"       => 43.20,
            "isActive"  => false,
            "id1C"      => "00021932",
        ],
        [
            "id"        => 332,
            "Product"   => "test Book 2",
            "rrc"       => 323.40,
            "isActive"  => true,
            "id1C"      => "0003932",
        ]
    ];

    const ARRAY_KEY_REQUEST = ["0", "id", "Product", "rrc", "isActive", "id1C", "1"];

    private AuthApiService $service;

    protected function setUp(): void
    {
        $kernel = self::bootKernel();
        $serviceContainer = $kernel->getContainer()->get('service_container');
        $this->service = new AuthApiService($serviceContainer);
    }


    public function testValidationSecretKey()
    {
        $arrData = self::ARRAY_KEY_REQUEST;
        sort($arrData);
        $key = implode($arrData). '123';
        $result = $this->service->auth1C(json_encode(self::ARRAY_DATA_REQUEST), sha1($key));
        $this->assertTrue($result);
    }

    public function testSendInvalidArray()
    {
        $arrData = self::ARRAY_KEY_REQUEST;
        sort($arrData);
        $key = implode($arrData). '123';
        $result = $this->service->auth1C(json_encode(self::ARRAY_DATA_REQUEST[0]), sha1($key));
        $this->assertFalse($result);
    }

}

