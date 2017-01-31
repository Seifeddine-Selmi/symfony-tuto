<?php
// src/Sdz/BlogBundle/Event/MessagePostEvent.php

namespace Sdz\BlogBundle\Event;

use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\Security\Core\User\UserInterface;

class MessagePostEvent extends Event
{
    protected $message;
    protected $user;

    public function __construct($message, UserInterface $user)
    {
        $this->message = $message;
        $this->user    = $user;
    }

    // Le listener doit avoir acc�s au message
    public function getMessage()
    {
        return $this->message;
    }

    // Le listener doit pouvoir modifier le message
    public function setMessage($message)
    {
        return $this->message = $message;
    }

    // Le listener doit avoir acc�s � l'utilisateur
    public function getUser()
    {
        return $this->user;
    }

    // Pas de setUser, les listeners ne peuvent pas modifier l'auteur du message !
}