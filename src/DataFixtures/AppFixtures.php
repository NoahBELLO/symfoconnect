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
            [
                'user'     => $users[0],
                'text'     => 'Bonjour tout le monde ! Ravi de rejoindre SymfoConnect.',
                'image'    => null,
                'location' => 'Paris, France',
            ],
            [
                'user'     => $users[0],
                'text'     => 'Une superbe journée pour coder en Symfony !',
                'image'    => null,
                'location' => 'Lyon, France',
            ],
            [
                'user'     => $users[1],
                'text'     => 'Je partage mes aventures de voyage ici.',
                'image'    => null,
                'location' => 'Bordeaux, France',
            ],
            [
                'user'     => $users[1],
                'text'     => 'Symfony 7 est vraiment impressionnant.',
                'image'    => null,
                'location' => null,
            ],
            [
                'user'     => $users[2],
                'text'     => 'Première publication, contente d\'être là !',
                'image'    => null,
                'location' => 'Marseille, France',
            ],
            [
                'user'     => $users[3],
                'text'     => 'Le café du matin avec du code, rien de mieux.',
                'image'    => null,
                'location' => 'Toulouse, France',
            ],
            [
                'user'     => $users[4],
                'text'     => 'Quelqu\'un d\'autre apprend Symfony ici ?',
                'image'    => null,
                'location' => 'Nantes, France',
            ],
            [
                'user'     => $users[4],
                'text'     => 'Les fixtures Doctrine sont vraiment pratiques pour les tests.',
                'image'    => null,
                'location' => null,
            ],
        ];

        foreach ($postsData as $data) {
            $post = new Post();
            $post->setUser($data['user']);
            $post->setText($data['text']);
            $post->setImage($data['image']);
            $post->setLocation($data['location']);
            $manager->persist($post);
        }

        $manager->flush();
    }
}
