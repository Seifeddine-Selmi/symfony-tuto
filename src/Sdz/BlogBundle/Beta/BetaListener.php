<?php
// src/Sdz/BlogBundle/Beta/BetaListener.php

namespace Sdz\BlogBundle\Beta;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;

class BetaListener
{
    // Notre processeur
    protected $betaHTML;

    // La date de fin de la version bêta :
    // - Avant cette date, on affichera un compte à rebours (J-3 par exemple)
    // - Après cette date, on n'affichera plus le « bêta »
    protected $endDate;

    public function __construct(BetaHTMLAdder $betaHTML, $endDate)
    {
        $this->betaHTML = $betaHTML;
        $this->endDate  = new \Datetime($endDate);
    }



    // L'argument de la méthode est un FilterResponseEvent
    public function processBeta(FilterResponseEvent $event)
    {

        // On teste si la requête est bien la requête principale (et non une sous-requête)
        if (!$event->isMasterRequest()) {
            return;
        }

       // $remainingDays = $this->endDate->diff(new \Datetime())->days;
        $remainingDays = date_diff($this->endDate, new \Datetime())->days;
        $response = $event->getResponse();
        // Si la date est dépassée, on ne fait rien
      /*  if ( $this->endDate > new \Datetime() || $remainingDays <= 0) {
            return;
        }*/



        // On récupère la réponse que le gestionnaire a insérée dans l'évènement
       // $response = $event->getResponse();


        // On utilise notre BetaHRML

        if ( $remainingDays > 0) {
            if ( $this->endDate > new \Datetime()) {
                $response = $this->betaHTML->addBeta($event->getResponse(), $remainingDays);
            }
        }


        // Ici on modifie comme on veut la réponse…

        // Puis on insère la réponse modifiée dans l'évènement
        $event->setResponse($response);

        // On stoppe la propagation de l'évènement en cours (ici, kernel.response)
        $event->stopPropagation();
    }
}