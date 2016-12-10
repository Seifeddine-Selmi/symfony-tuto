<?php
// src/Sdz/BlogBundle/DataFixtures/ORM/Competences.php

namespace Sdz\BlogBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Sdz\BlogBundle\Entity\Competence;

class Competences implements FixtureInterface
{
    public function load(ObjectManager $manager)
    {
        // Liste des noms de compétences à ajouter
        $names = array('Doctrine', 'Formulaire', 'Twig');

        foreach($names as $i => $name)
        {
            // On crée la compétence
            $competences_list[$i] = new Competence();
            $competences_list[$i]->setName($name);

            // On la persiste
            $manager->persist($competences_list[$i]);
        }

        // On déclenche l'enregistrement
        $manager->flush();
    }
}