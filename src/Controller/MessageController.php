<?php

namespace App\Controller;

use App\Entity\Message;
use App\Entity\User;
use App\Message\SendPrivateMessageNotification;
use App\Repository\MessageRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
final class MessageController extends AbstractController
{
    #[Route('/messages', name: 'app_messages')]
    public function index(MessageRepository $messageRepository): Response
    {
        /** @var User $currentUser */
        $currentUser = $this->getUser();

        $conversations = $messageRepository->findConversationsForUser($currentUser);

        return $this->render('message/index.html.twig', [
            'conversations' => $conversations,
        ]);
    }

    #[Route('/messages/{username}', name: 'app_messages_show')]
    public function show(
        string $username,
        Request $request,
        UserRepository $userRepository,
        MessageRepository $messageRepository,
        EntityManagerInterface $em,
        MessageBusInterface $bus,
    ): Response {
        /** @var User $currentUser */
        $currentUser = $this->getUser();

        $partner = $userRepository->findOneBy(['username' => $username]);
        if (!$partner || $partner === $currentUser) {
            throw $this->createNotFoundException('Utilisateur introuvable.');
        }

        // Marquer les messages reçus comme lus
        $messages = $messageRepository->findConversationBetween($currentUser, $partner);
        foreach ($messages as $message) {
            if ($message->getRecipient() === $currentUser && !$message->isRead()) {
                $message->setIsRead(true);
            }
        }
        $em->flush();

        // Traitement du formulaire d'envoi
        if ($request->isMethod('POST')) {
            $content = trim($request->request->get('content', ''));
            if ($content !== '') {
                $newMessage = new Message();
                $newMessage->setSender($currentUser);
                $newMessage->setRecipient($partner);
                $newMessage->setContent($content);
                $em->persist($newMessage);
                $em->flush();

                // Notification email asynchrone
                $bus->dispatch(new SendPrivateMessageNotification(
                    $currentUser->getEmail(),
                    $partner->getEmail(),
                    $partner->getUsername(),
                    $content,
                ));
            }

            return $this->redirectToRoute('app_messages_show', ['username' => $username]);
        }

        return $this->render('message/show.html.twig', [
            'partner' => $partner,
            'messages' => $messages,
        ]);
    }
}
