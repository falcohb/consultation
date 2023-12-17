<?php

namespace App\Tests\Functional\Controller\Admin;

use App\Factory\UserFactory;
use App\Repository\UserRepository;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class DashboardControllerTest extends WebTestCase
{
    use ResetDatabase;
    use Factories;

    public function testRedirectToLogin(): void
    {
        $client = static::createClient();
        $router = static::getContainer()->get(RouterInterface::class);
        $adminUrlGenerator = static::getContainer()->get(AdminUrlGenerator::class);

        $client->request('GET', $adminUrlGenerator->generateUrl());
        $this->assertResponseRedirects($router->generate('app_login'), 302);
    }

    public function testRoleUserAccessDenied(): void
    {
        $client = static::createClient();
        $router = static::getContainer()->get(RouterInterface::class);
        $adminUrlGenerator = static::getContainer()->get(AdminUrlGenerator::class);

        /** @var UserInterface $regularUser */
        $regularUser = UserFactory::createOne(['roles' => ['ROLE_USER'], 'email' => uuid_create() . '@test.be'])
            ->object();

        $client->loginUser($regularUser);

        // user is now logged in, so you can test homepage
        $client->request('GET', $router->generate('app_home'));
        $this->assertResponseIsSuccessful();

        // user is logged in, but ROLE_USER cannot access to the admin dashboard
        $client->request('GET', $adminUrlGenerator->generateUrl());
        $this->assertResponseStatusCodeSame(403);
    }

    public function testAllowedRolesAccessGranted(): void
    {
        $client = static::createClient();
        $router = static::getContainer()->get(RouterInterface::class);
        $adminUrlGenerator = static::getContainer()->get(AdminUrlGenerator::class);

        /** @var UserInterface $adminUser */
        $adminUser = UserFactory::createOne(['roles' => ['ROLE_ADMIN'], 'email' => uuid_create() . '@test.be'])
            ->object();
        $client->loginUser($adminUser);

        // user is now logged in, so you can test homepage
        $client->request('GET', $router->generate('app_account'));
        $this->assertResponseIsSuccessful();

        // user is logged in, but ROLE_USER cannot access to the admin dashboard
        $client->request('GET', $adminUrlGenerator->generateUrl());
        $this->assertResponseIsSuccessful('Role: ROLE_ADMIN');
        $this->assertSelectorTextContains('h1', 'Tableau de bord');
    }
}
