<?php
// src/Sdz/BlogBundle/Validator/AntiFlood.php
namespace Sdz\BlogBundle\Validator;
use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class Antiflood extends Constraint
{
    public $message = "Vous avez d�j� post� un message il y a moins de 15 secondes, merci d'attendre un peu.";

    public function validatedBy()
    {
        return 'sdz_blog_antiflood'; // Ici, on fait appel � l'alias du service
    }
}