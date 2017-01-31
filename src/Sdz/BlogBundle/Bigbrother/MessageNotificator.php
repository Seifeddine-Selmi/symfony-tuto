<?php
// src/Sdz/BlogBundle/Bigbrother/MessageNotificator.php

namespace Sdz\BlogBundle\Bigbrother;

use Symfony\Component\Security\Core\User\UserInterface;

class MessageNotificator
{
    protected $mailer;

    public function __construct(\Swift_Mailer $mailer)
    {
        $this->mailer = $mailer;
    }

    // Méthode pour notifier par e-mail un administrateur
    public function notifyByEmail($message, UserInterface $user)
    {
        $message = \Swift_Message::newInstance()
            ->setSubject("Nouveau message d'un utilisateur surveillé")
            ->setFrom('admin@votresite.com')
            ->setTo('admin@votresite.com')
            ->setBody("L'utilisateur surveillé '".$user->getUsername()."' a posté le message suivant : '".$message."'")
        ;

        $this->mailer->send($message);
    }
}
