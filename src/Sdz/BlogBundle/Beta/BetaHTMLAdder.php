<?php
// src/Sdz/BlogBundle/Beta/BetaHTMLAdder.php

namespace Sdz\BlogBundle\Beta;

use Symfony\Component\HttpFoundation\Response;

class BetaHTMLAdder
{
    // M�thode pour ajouter le � b�ta � � une r�ponse
    public function addBeta(Response $response, $remainingDays)
    {
        $content = $response->getContent();


        $html = '<div style="position: absolute; top: 0; background: orange; width: 100%; text-align: center; padding: 0.5em;">Beta J-'.(int) $remainingDays.' !</div>';


        $content = str_replace(
            '<body>',
            '<body> '.$html,
            $content
        );


        $response->setContent($content);

        return $response;
    }
}