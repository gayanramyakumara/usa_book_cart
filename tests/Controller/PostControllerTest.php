<?php 

// tests/Controller/PostControllerTest.php
namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class PostControllerTest extends WebTestCase
{
    public function testShowPost()
    {
        $client = static::createClient();

        $client->request('POST', '/index');
        $this->assertSelectorTextContains('body h3', 'Book Store : Home page');
       // $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }
}


