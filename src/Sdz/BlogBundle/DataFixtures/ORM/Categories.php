<?php
// src/Sdz/BlogBundle/DataFixtures/ORM/Categories.php

namespace Sdz\BlogBundle\DataFixtures\ORM;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Sdz\BlogBundle\Entity\Category;

class Categories implements FixtureInterface
{
    // Dans l'argument de la m�thode load, l'objet $manager est l'EntityManager
    public function load(ObjectManager $manager)
    {
        // Liste des noms de cat�gorie � ajouter
        $names = array('Symfony2', 'Doctrine2', 'Tutoriel');
        foreach($names as $i => $name)
        {
            // On cr�e la cat�gorie
            $categories_list[$i] = new Category();
            $categories_list[$i]->setName($name);
            // On la persiste
            $manager->persist($categories_list[$i]);
        }
        // On d�clenche l enregistrement
        $manager->flush();
    }
}