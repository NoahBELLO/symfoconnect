<?php

namespace App\Repository;

use App\Entity\Message;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Message>
 */
class MessageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Message::class);
    }

    /**
     * Retourne la liste des interlocuteurs uniques d'un utilisateur,
     * avec le dernier message de chaque conversation.
     *
     * @return array<int, array{partner: User, lastMessage: Message}>
     */
    public function findConversationsForUser(User $user): array
    {
        $messages = $this->createQueryBuilder('m')
            ->where('m.sender = :user OR m.recipient = :user')
            ->setParameter('user', $user)
            ->orderBy('m.createdAt', 'DESC')
            ->getQuery()
            ->getResult();

        $conversations = [];
        foreach ($messages as $message) {
            $partner = $message->getSender() === $user
                ? $message->getRecipient()
                : $message->getSender();

            $partnerId = $partner->getId();
            if (!isset($conversations[$partnerId])) {
                $conversations[$partnerId] = [
                    'partner' => $partner,
                    'lastMessage' => $message,
                ];
            }
        }

        return array_values($conversations);
    }

    /**
     * Retourne tous les messages échangés entre deux utilisateurs, par ordre chronologique.
     *
     * @return Message[]
     */
    public function findConversationBetween(User $userA, User $userB): array
    {
        return $this->createQueryBuilder('m')
            ->where('(m.sender = :a AND m.recipient = :b) OR (m.sender = :b AND m.recipient = :a)')
            ->setParameter('a', $userA)
            ->setParameter('b', $userB)
            ->orderBy('m.createdAt', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
