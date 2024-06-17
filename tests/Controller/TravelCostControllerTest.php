<?php

namespace App\Tests\Controller;

use Exception;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TravelCostControllerTest extends WebTestCase
{
    public function testCalculateTravelCost()
    {
        try {
            $client = static::createClient();

            $postData = [
                'travel_start_date' => '2024-07-01',
                'children_ages' => [4, 8, 14],
                'payment_date' => '2024-06-15'
            ];

            $client->request('POST', '/calculate-travel-cost', [], [], [], json_encode($postData));

            $this->assertEquals(200, $client->getResponse()->getStatusCode());

            $responseData = json_decode($client->getResponse()->getContent(), true);
            $this->assertArrayHasKey('total_cost', $responseData);

        } catch (Exception $e) {
            echo $e->getMessage();
        } finally {
            restore_exception_handler(); // Восстанавливаем обработчик исключений
        }
    }
}

