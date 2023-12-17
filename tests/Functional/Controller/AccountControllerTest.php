<?php

namespace App\Tests\Functional\Controller;

use App\Factory\PatientFactory;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class AccountControllerTest extends WebTestCase
{
    use ResetDatabase;
    use Factories;

    public function testRouteNotLoggedInRedirectToLogin(): void
    {
        $client = static::createClient();
        $client->request('GET', '/account');

        $this->assertResponseRedirects('/login', 302);
    }

    public function testProjectAddRouteNotLoggedInIsWorking(): void
    {
        $client = static::createClient();
        $router = static::getContainer()->get(RouterInterface::class);
        $client->request('GET', $router->generate('app_account'));

        $this->assertResponseRedirects('/login', 302);
    }

    public function testRouteIsWorking(): void
    {
        $client = static::createClient();
        $router = static::getContainer()->get(RouterInterface::class);

        /** @var UserInterface $regularUser */
        $regularUser = PatientFactory::createOne(['roles' => ['ROLE_USER'], 'email' => uuid_create() . '@test.be'])
            ->object();
        $client->loginUser($regularUser);

        $client->request('GET', $router->generate('app_account'));

        $this->assertResponseStatusCodeSame(200);
    }

    public function testDeleteAccount(): void
    {
        $client = static::createClient();
        $router = static::getContainer()->get(RouterInterface::class);

        /** @var UserInterface $regularUser */
        $regularUser = PatientFactory::createOne(['roles' => ['ROLE_USER'], 'email' => uuid_create() . '@test.be'])
            ->object();
        $client->loginUser($regularUser);

        $client->request('GET', $router->generate('app_account_delete'));
        $client->followRedirect();

        $this->assertResponseIsSuccessful();
        $this->assertAnySelectorTextContains('div', 'Votre compte a été supprimé!');
    }
}
