<?php
namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UserControllerTest extends WebTestCase
{

    public function testDashboard()
    {
        $client = static::createClient();

        $client->request('GET', '/dashboard');

        $this->assertEquals(302, $client->getResponse()->getStatusCode());
    }

    public function testMessages()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/login');

        $form = $crawler->selectButton('submit')->form();

        $form['username'] = 'admin@hotmail.com';
        $form['password'] = 'admin';

        $crawler = $client->submit($form);

        //fwrite(STDERR, print_r($client->request('GET', ''), TRUE));
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }




}