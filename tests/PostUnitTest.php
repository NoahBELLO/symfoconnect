<?php

namespace App\Tests;

use App\Entity\Post;
use App\Entity\User;
use PHPUnit\Framework\TestCase;

class PostUnitTest extends TestCase
{
    public function testIsLikedByReturnsFalseWhenNoLikes(): void
    {
        $user = new User();
        $post = new Post();

        $this->assertFalse($post->isLikedBy($user));
    }

    public function testIsLikedByReturnsTrueAfterAddingLike(): void
    {
        $user = new User();
        // Simuler un ID via réflexion pour que la comparaison fonctionne
        $reflection = new \ReflectionClass($user);
        $prop = $reflection->getProperty('id');
        $prop->setValue($user, 1);

        $post = new Post();
        $post->addLikedBy($user);

        $this->assertTrue($post->isLikedBy($user));
    }
}
