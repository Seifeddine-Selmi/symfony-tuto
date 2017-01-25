<?php
// src/Sdz/BlogBundle/Twig/AntispamExtension.php

namespace Sdz\BlogBundle\Twig;

use Sdz\BlogBundle\Antispam\SdzAntispam;

class AntispamExtension extends \Twig_Extension
{
    /**
     * @var SdzAntispam
     */
    private $sdzAntispam;

    public function __construct(SdzAntispam $sdzAntispam)
    {
        $this->sdzAntispam = $sdzAntispam;
    }

    public function checkIfArgumentIsSpam($text)
    {
        return $this->sdzAntispam->isSpam($text);
    }

    // Twig va exécuter cette méthode pour savoir quelle(s) fonction(s) ajoute notre service
    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('checkIfSpam', array($this, 'checkIfArgumentIsSpam')), //S3
           //  new \Twig_SimpleFunction('checkIfSpam', array($this->sdzAntispam, 'isSpam')), //S3
           // 'checkIfSpam' => new \Twig_Function_Method($this, 'isSpam') //S2

        );
    }

    // La méthode getName() identifie votre extension Twig, elle est obligatoire
    public function getName()
    {
        return 'SdzAntispam';
    }
}