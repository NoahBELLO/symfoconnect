<?php

namespace App\Tests;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class HomepageTest extends WebTestCase
{
    // Test 1 : la page publique répond en 200
    public function testHomepageIsAccessible(): void
    {
        $client = static::createClient();
        $client->request('GET', '/');

        $this->assertResponseIsSuccessful();
    }

    // Test 2 : /post/nouveau redirige vers /login si non connecté
    public function testNewPostRedirectsToLoginWhenNotAuthenticated(): void
    {
        $client = static::createClient();
        $client->request('GET', '/post/nouveau');

        $this->assertResponseRedirects('/login');
    }

    // Test 3 : un utilisateur connecté peut accéder au formulaire de post
    public function testNewPostIsAccessibleWhenAuthenticated(): void
    {
        $client = static::createClient();

        $container = static::getContainer();
        $em = $container->get('doctrine')->getManager();

        /** @var UserPasswordHasherInterface $hasher */
        $hasher = $container->get(UserPasswordHasherInterface::class);

        $user = new User();
        $user->setEmail('testpost@example.com');
        $user->setUsername('testpost');
        $user->setPassword($hasher->hashPassword($user, 'password123'));
        $em->persist($user);
        $em->flush();

        $client->loginUser($user);
        $client->request('GET', '/post/nouveau');

        $this->assertResponseIsSuccessful();

        $em->remove($user);
        $em->flush();
    }
}
