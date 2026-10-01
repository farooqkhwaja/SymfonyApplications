<?php

namespace App\MessageHandler;

use App\Message\Comment;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class CommentHandler{
    public function __invoke(Comment $message): void
    {
        // do something with your message
    }
}
