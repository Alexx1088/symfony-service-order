<?php

namespace App\Tests\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;

class OrderControllerTest extends WebTestCase
{
    private \Symfony\Bundle\FrameworkBundle\KernelBrowser $client;

    protected function setUp(): void
    {
        static::ensureKernelShutdown();

        $this->client = static::createClient();

        $container = static::getContainer();
        $entityManager = $container->get(EntityManagerInterface::class);

        $purger = new ORMPurger($entityManager);
        $purger->purge();
    }

    public function testGuestCannotAccessOrderForm(): void
    {
        $this->client->request('GET', '/order');
        $this->assertResponseRedirects('/login');
    }

    public function testAuthenticatedUserCanSeeOrderForm(): void
    {
        $user = $this->createTestUser();
        $this->client->loginUser($user);

        $crawler = $this->client->request('GET', '/order');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('form');
        $this->assertSelectorExists('select[name="order_type_form[service]"]');
        $this->assertSelectorExists('input[name="order_type_form[email]"]');
        $this->assertSelectorExists('button[type="submit"]');
    }

    public function testSubmitInvalidFormShowsErrors(): void
    {
        $user = $this->createTestUser();
        $this->client->loginUser($user);

        $crawler = $this->client->request('GET', '/order');
        $this->assertResponseIsSuccessful(); // Ensure the form page loads successfully

        $form = $crawler->selectButton('Подтвердить')->form();
        $form['order_type_form[service]'] = '';
        $form['order_type_form[email]'] = '';

        $crawler = $this->client->submit($form);

        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('ul li');
    }

    public function testSuccessfulOrderSubmissionPersistsOrder(): void
    {
        $user = $this->createTestUser();
        $this->client->loginUser($user);

        $crawler = $this->client->request('GET', '/order');
        $this->assertResponseIsSuccessful();

        $form = $crawler->selectButton('Подтвердить')->form();
        $form['order_type_form[service]'] = 'Оценка стоимости бизнеса';
        $form['order_type_form[email]'] = 'test@example.com';
        $form['order_type_form[price]'] = 15000;

        $this->client->submit($form);

        $this->assertResponseRedirects('/order/success');
        $this->client->followRedirect();
        $this->assertSelectorExists('h1');

        $order = self::getContainer()->get(\App\Repository\OrderRepository::class)
            ->findOneBy(['email' => 'test@example.com']);

        $this->assertNotNull($order);
        $this->assertSame('Оценка стоимости бизнеса', $order->getService());
    }

    private function createTestUser(): User
    {
        $entityManager = self::getContainer()->get(EntityManagerInterface::class);

        $user = new User();
        $user->setEmail('testuser@example.com');

        $passwordHasher = self::getContainer()->get(\Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface::class);
        $hashedPassword = $passwordHasher->hashPassword($user, 'password');
        $user->setPassword($hashedPassword);

        $entityManager->persist($user);
        $entityManager->flush();

        return $user;
    }
}
