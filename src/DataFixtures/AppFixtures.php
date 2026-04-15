<?php

namespace App\DataFixtures;

use App\Entity\Post;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    public function __construct(private UserPasswordHasherInterface $hasher)
    {
    }

    public function load(ObjectManager $manager): void
    {
        $usersData = [
            ['email' => 'alice@example.com', 'username' => 'Alice',   'roles' => ['ROLE_ADMIN'], 'password' => 'password123'],
            ['email' => 'bob@example.com',   'username' => 'Bob',     'roles' => [],             'password' => 'password123'],
            ['email' => 'carol@example.com', 'username' => 'Carol',   'roles' => [],             'password' => 'password123'],
            ['email' => 'david@example.com', 'username' => 'David',   'roles' => [],             'password' => 'password123'],
            ['email' => 'emma@example.com',  'username' => 'Emma',    'roles' => [],             'password' => 'password123'],
        ];

        $users = [];
        foreach ($usersData as $data) {
            $user = new User();
            $user->setEmail($data['email']);
            $user->setUsername($data['username']);
            $user->setRoles($data['roles']);
            $user->setPassword($this->hasher->hashPassword($user, $data['password']));
            $manager->persist($user);
            $users[] = $user;
        }

        $postsData = [
            ['author' => $users[0], 'content' => 'Bonjour tout le monde ! Ravi de rejoindre SymfoConnect.'],
            ['author' => $users[0], 'content' => 'Une superbe journée pour coder en Symfony !'],
            ['author' => $users[1], 'content' => 'Je partage mes aventures de voyage ici.'],
            ['author' => $users[1], 'content' => 'Symfony 7 est vraiment impressionnant.'],
            ['author' => $users[2], 'content' => 'Première publication, contente d\'être là !'],
            ['author' => $users[3], 'content' => 'Le café du matin avec du code, rien de mieux.'],
            ['author' => $users[4], 'content' => 'Quelqu\'un d\'autre apprend Symfony ici ?'],
            ['author' => $users[4], 'content' => 'Les fixtures Doctrine sont vraiment pratiques pour les tests.'],
        ];

        foreach ($postsData as $data) {
            $post = new Post();
            $post->setAuthor($data['author']);
            $post->setContent($data['content']);
            $manager->persist($post);
        }

        $manager->flush();
    }
}
