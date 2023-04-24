<?php

namespace App\Tests\Controller;

class HomeControllerTest extends AppWebTestCase
{
    public function testHomepageRedirects()
    {
        $this->client->request('GET', '/');
        $this->assertResponseRedirects('/octopus');
    }
}
