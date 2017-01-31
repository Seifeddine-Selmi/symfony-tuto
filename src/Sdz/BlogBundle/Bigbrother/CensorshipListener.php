<?php
// src/Sdz/BlogBundle/Bigbrother/CensorshipListener.php

namespace Sdz\BlogBundle\Bigbrother;

use Sdz\BlogBundle\Event\BlogEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class MessageListener implements EventSubscriberInterface
{
    // La méthode de l'interface que l'on doit implémenter, à définir en static
    static public function getSubscribedEvents()
    {
        // On retourne un tableau « nom de l'évènement » => « méthode à exécuter »
      /*  return array(
            BlogEvents::POST_MESSAGE    => 'processMessage',
            BlogEvents::AUTRE_EVENEMENT => 'autreMethode',
            // ...
        );*/

        return array(
            BlogEvents::POST_MESSAGE => array('processMessage' => 2)
        );
    }

    public function processMessage(MessagePostEvent $event)
    {
        // ...
    }

    public function autreMethode()
    {
        // ...
    }
}