<?php

namespace App\Message;

use Symfony\Component\Messenger\Attribute\AsMessage;

#[AsMessage('async')]
final class SendPrivateMessageNotification
{
    public function __construct(
        public readonly string $senderEmail,
        public readonly string $recipientEmail,
        public readonly string $recipientUsername,
        public readonly string $messageContent,
    ) {
    }
}
