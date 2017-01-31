<?php
// src/Sdz/BlogBundle/Beta/BetaListener.php

namespace Sdz\BlogBundle\Beta;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;

class BetaListener
{
    // Notre processeur
    protected $betaHTML;

    // La date de fin de la version b�ta :
    // - Avant cette date, on affichera un compte � rebours (J-3 par exemple)
    // - Apr�s cette date, on n'affichera plus le � b�ta �
    protected $endDate;

    public function __construct(BetaHTMLAdder $betaHTML, $endDate)
    {
        $this->betaHTML = $betaHTML;
        $this->endDate  = new \Datetime($endDate);
    }



    // L'argument de la m�thode est un FilterResponseEvent
    public function processBeta(FilterResponseEvent $event)
    {

        // On teste si la requ�te est bien la requ�te principale (et non une sous-requ�te)
        if (!$event->isMasterRequest()) {
            return;
        }

       // $remainingDays = $this->endDate->diff(new \Datetime())->days;
        $remainingDays = date_diff($this->endDate, new \Datetime())->days;
        $response = $event->getResponse();
        // Si la date est d�pass�e, on ne fait rien
      /*  if ( $this->endDate > new \Datetime() || $remainingDays <= 0) {
            return;
        }*/



        // On r�cup�re la r�ponse que le gestionnaire a ins�r�e dans l'�v�nement
       // $response = $event->getResponse();


        // On utilise notre BetaHRML

        if ( $remainingDays > 0) {
            if ( $this->endDate > new \Datetime()) {
                $response = $this->betaHTML->addBeta($event->getResponse(), $remainingDays);
            }
        }


        // Ici on modifie comme on veut la r�ponse�

        // Puis on ins�re la r�ponse modifi�e dans l'�v�nement
        $event->setResponse($response);

        // On stoppe la propagation de l'�v�nement en cours (ici, kernel.response)
        $event->stopPropagation();
    }
}