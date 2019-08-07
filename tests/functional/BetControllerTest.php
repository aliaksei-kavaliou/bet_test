<?php declare(strict_types = 1);

namespace App\Tests\functional;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class BetControllerTest extends WebTestCase
{
    public const URL = '/v1/bet';

    /** @dataProvider dataProvider */
    public function testPostTransaction($status, $body, $expectedString): void
    {
        $client = static::createClient();
        $client->request(
            'POST',
            self::URL,
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            \json_encode($body)
        );

        $this->assertEquals($status, $client->getResponse()->getStatusCode());

        if (!empty($expectedString)) {
            $this->assertContains($expectedString, $client->getResponse()->getContent());
        }
    }

    public function dataProvider(): array
    {
        return [
            [
                'status' => 400,
                'body' => [
                    "player_id" => 1,
                    "stake_amount" => "50000",
                    "selections" => [
                        [
                            "id" => 1,
                            "odds" => "1.601",
                        ],
                    ],
                ],
                'expectedString' => 'Maximum stake amount is ',
            ],
            [
                'status' => 201,
                'body' => [
                    "player_id" => 1,
                    "stake_amount" => "5",
                    "selections" => [
                        [
                            "id" => 1,
                            "odds" => "1.601",
                        ],
                    ],
                ],
                'expectedString' => null,
            ],
        ];
    }
}
