<?php
// src/Sdz/BlogBundle/Validator/AntiFlood.php
namespace Sdz\BlogBundle\Validator;
use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class Antiflood extends Constraint
{
    public $message = "Vous avez déjà posté un message il y a moins de 15 secondes, merci d'attendre un peu.";

    public function validatedBy()
    {
        return 'sdz_blog_antiflood'; // Ici, on fait appel à l'alias du service
    }
}