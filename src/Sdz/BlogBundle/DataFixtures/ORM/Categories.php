<?php
// src/Sdz/BlogBundle/DataFixtures/ORM/Categories.php

namespace Sdz\BlogBundle\DataFixtures\ORM;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Sdz\BlogBundle\Entity\Category;

class Categories implements FixtureInterface
{
    // Dans l'argument de la méthode load, l'objet $manager est l'EntityManager
    public function load(ObjectManager $manager)
    {
        // Liste des noms de catégorie à ajouter
        $names = array('Symfony2', 'Doctrine2', 'Tutoriel');
        foreach($names as $i => $name)
        {
            // On crée la catégorie
            $categories_list[$i] = new Category();
            $categories_list[$i]->setName($name);
            // On la persiste
            $manager->persist($categories_list[$i]);
        }
        // On déclenche l enregistrement
        $manager->flush();
    }
}