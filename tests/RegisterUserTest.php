<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class RegisterUserTest extends WebTestCase
{
    public function testSomething(): void
    {

        /**
         * 
         */


        $client = static::createClient();
        $client->request('GET', '/inscription');

        $client->submitForm('Valider', [
            'register_user[firstname]' => 'mahou',
            'register_user[lastname]' => 'sow',
            'register_user[email]' => 'mahou@sow.com',
            'register_user[plainPassword][first]' => 'mahou123',
            'register_user[plainPassword][second]' => 'mahou123'
        ]);

        $this->assertResponseRedirects('/connexion');
        $client->followRedirect();


        $this->assertSelectorExists('div:contains("Votre inscription a été réussie.")');



    }
}
