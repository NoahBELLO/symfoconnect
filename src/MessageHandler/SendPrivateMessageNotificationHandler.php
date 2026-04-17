<?php

namespace App\MessageHandler;

use App\Message\SendPrivateMessageNotification;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Mime\Email;

#[AsMessageHandler]
final class SendPrivateMessageNotificationHandler
{
    public function __construct(private MailerInterface $mailer)
    {
    }

    public function __invoke(SendPrivateMessageNotification $message): void
    {
        $email = (new Email())
            ->from('noreply@symfoconnect.com')
            ->to($message->recipientEmail)
            ->subject('Nouveau message privé sur SymfoConnect')
            ->text(sprintf(
                "Bonjour %s,\n\nVous avez reçu un nouveau message de %s :\n\n\"%s\"\n\nConnectez-vous pour répondre.",
                $message->recipientUsername,
                $message->senderEmail,
                $message->messageContent,
            ));

        $this->mailer->send($email);
    }
}
