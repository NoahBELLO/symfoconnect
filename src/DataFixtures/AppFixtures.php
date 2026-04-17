<?php

namespace App\DataFixtures;

use App\Entity\Message;
use App\Entity\Notification;
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
        // --- Utilisateurs ---
        $usersData = [
            ['email' => 'alice@example.com', 'username' => 'Alice', 'roles' => ['ROLE_ADMIN'], 'password' => 'password123'],
            ['email' => 'bob@example.com',   'username' => 'Bob',   'roles' => [],             'password' => 'password123'],
            ['email' => 'carol@example.com', 'username' => 'Carol', 'roles' => [],             'password' => 'password123'],
            ['email' => 'david@example.com', 'username' => 'David', 'roles' => [],             'password' => 'password123'],
            ['email' => 'emma@example.com',  'username' => 'Emma',  'roles' => [],             'password' => 'password123'],
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

        [$alice, $bob, $carol, $david, $emma] = $users;

        // --- Posts ---
        $postsData = [
            ['author' => $alice, 'content' => 'Bonjour tout le monde ! Ravi de rejoindre SymfoConnect.'],
            ['author' => $alice, 'content' => 'Une superbe journée pour coder en Symfony !'],
            ['author' => $bob,   'content' => 'Je partage mes aventures de voyage ici.'],
            ['author' => $bob,   'content' => 'Symfony 7 est vraiment impressionnant.'],
            ['author' => $carol, 'content' => 'Première publication, contente d\'être là !'],
            ['author' => $david, 'content' => 'Le café du matin avec du code, rien de mieux.'],
            ['author' => $emma,  'content' => 'Quelqu\'un d\'autre apprend Symfony ici ?'],
            ['author' => $emma,  'content' => 'Les fixtures Doctrine sont vraiment pratiques pour les tests.'],
        ];

        $posts = [];
        foreach ($postsData as $data) {
            $post = new Post();
            $post->setAuthor($data['author']);
            $post->setContent($data['content']);
            $manager->persist($post);
            $posts[] = $post;
        }

        // --- Follows ---
        // Alice suit Bob et Carol
        $alice->addFollowing($bob);
        $alice->addFollowing($carol);

        // Bob suit Alice et Emma
        $bob->addFollowing($alice);
        $bob->addFollowing($emma);

        // Carol suit Alice
        $carol->addFollowing($alice);

        // David suit Emma
        $david->addFollowing($emma);

        // --- Likes ---
        // Bob et Carol likent le 1er post d'Alice
        $posts[0]->addLikedBy($bob);
        $posts[0]->addLikedBy($carol);

        // Alice et David likent le post de Bob
        $posts[2]->addLikedBy($alice);
        $posts[2]->addLikedBy($david);

        // Alice like le post de Carol
        $posts[4]->addLikedBy($alice);

        // Bob et Emma likent le post de David
        $posts[5]->addLikedBy($bob);
        $posts[5]->addLikedBy($emma);

        // --- Notifications de follow ---
        $followNotifs = [
            [$bob,   'Alice vous suit maintenant.',  $alice],
            [$carol, 'Alice vous suit maintenant.',  $alice],
            [$alice, 'Bob vous suit maintenant.',    $bob],
            [$emma,  'Bob vous suit maintenant.',    $bob],
            [$alice, 'Carol vous suit maintenant.',  $carol],
            [$emma,  'David vous suit maintenant.',  $david],
        ];

        foreach ($followNotifs as [$recipient, $content, $sender]) {
            $notif = new Notification();
            $notif->setRecipient($recipient);
            $notif->setType('follow');
            $notif->setContent($content);
            $manager->persist($notif);
        }

        // --- Messages privés ---
        $messagesData = [
            ['sender' => $alice, 'recipient' => $bob,   'content' => 'Salut Bob, comment ça va ?'],
            ['sender' => $bob,   'recipient' => $alice,  'content' => 'Très bien Alice, merci !'],
            ['sender' => $alice, 'recipient' => $bob,    'content' => 'Super, à bientôt !'],
            ['sender' => $carol, 'recipient' => $alice,  'content' => 'Alice, tu as vu le dernier post ?'],
            ['sender' => $alice, 'recipient' => $carol,  'content' => 'Oui, très intéressant !'],
        ];

        foreach ($messagesData as $data) {
            $message = new Message();
            $message->setSender($data['sender']);
            $message->setRecipient($data['recipient']);
            $message->setContent($data['content']);
            $manager->persist($message);
        }

        $manager->flush();
    }
}
