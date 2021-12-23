<?php

namespace App\Tests\Functional\Controllers;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\BrowserKit\AbstractBrowser;

class IntegrationFor1CControllerTest extends WebTestCase
{
    const ARR_DATA_SEND = [
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

    private string $uri = '/integrationfor1c/savePacketData1c';
    private string $method = 'POST';
    private string $jsonData;
    private string $auth = '2b1fe338234350a1f61e93b4a491809022e763ac';
    private AbstractBrowser $client;
    private string $secretKey = '123';
    private string $invalidDataJson;

    protected function setUp(): void
    {
        $arrInvalid = [
            [
                'Product'         => 'test Book 3',
                'te21'            => '124',
                'sa1'             => 25,
                'id1c'            => 00000000455,
                'itf14'           => 646005031,
                'vfe5'            => '12',
            ],
        ];

        $this->jsonData = json_encode(self::ARR_DATA_SEND);
        $this->invalidDataJson = json_encode($arrInvalid);

        $this->client = static::createClient();
    }

    public function testBadRequestApi() : void
    {
        $this->client->request(
            $this->method,
            $this->uri,
            [
                "data" => $this->jsonData,
            ]
        );

        $response = $this->client->getResponse();
        $this->assertEquals(400, $response->getStatusCode());
    }

    public function testAuthorizationFailed() : void
    {
        $this->client->request(
            $this->method,
            $this->uri,
            [
                "data" => $this->jsonData,
                "auth" => sha1('123321'),
            ]
        );

        $response = $this->client->getResponse();
        $this->assertEquals(400, $response->getStatusCode());
    }

    public function testInvalidDataRequest() : void
    {
        $this->client->request(
            $this->method,
            $this->uri,
            [
                "data" => $this->invalidDataJson,
                "auth" => $this->auth,
            ]
        );

        $response = $this->client->getResponse();
        $this->assertEquals(400, $response->getStatusCode());
    }

    public function testAuthorizationActive() : void
    {
        $this->client->request(
            $this->method,
            $this->uri,
            [
                "data" => $this->jsonData,
                "auth" => $this->auth,
            ]
        );

        $response = $this->client->getResponse();
        $this->assertEquals(200, $response->getStatusCode());
    }
}

