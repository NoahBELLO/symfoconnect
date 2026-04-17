<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ApiPostTest extends WebTestCase
{
    // Test 4 : GET /api/posts retourne du JSON valide
    public function testApiPostsReturnsValidJson(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api/posts', [], [], [
            'HTTP_ACCEPT' => 'application/ld+json',
        ]);

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');

        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertIsArray($data);
        $this->assertArrayHasKey('member', $data);
    }
}
