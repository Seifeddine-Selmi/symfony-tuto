<?php
// src/Sdz/BlogBundle/Validator/AntifloodValidator.php
namespace Sdz\BlogBundle\Validator;


use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;


class AntifloodValidator extends ConstraintValidator
{
    private $requestStack;
    private $em;

    // Les arguments d�clar�s dans la d�finition du service arrivent au constructeur
    // On doit les enregistrer dans l'objet pour pouvoir s'en resservir dans la m�thode validate()
    public function __construct(RequestStack $requestStack, EntityManagerInterface $em)
    {
        $this->requestStack = $requestStack;
        $this->em           = $em;
    }

    public function validateTest($value, Constraint $constraint)
    {
        // Pour r�cup�rer l'objet Request tel qu'on le connait, il faut utiliser getCurrentRequest du service request_stack
        $request = $this->requestStack->getCurrentRequest();

        // On r�cup�re l'IP de celui qui poste
        $ip = $request->getClientIp();

        // On v�rifie si cette IP a d�j� post� une candidature il y a moins de 15 secondes
        $isFlood = $this->em
            ->getRepository('SdzBlogBundle:Comment')
            ->isFlood($ip, 15) // Bien entendu, il faudrait �crire cette m�thode isFlood, c'est pour l'exemple
        ;

        if ($isFlood) {
            // C'est cette ligne qui d�clenche l'erreur pour le formulaire, avec en argument le message
            $this->context->addViolation($constraint->message);
        }
    }

    public function validate($value, Constraint $constraint)
    {
        // Pour l'instant, on consid�re comme flood tout message de moins de 3 caract�res
        if (strlen($value) < 3) {
            // C'est cette ligne qui d�clenche l'erreur pour le formulaire, avec en argument le message
           // $this->context->addViolation($constraint->message);

            $this->context
                ->buildViolation($constraint->message)
                ->setParameters(array('%string%' => $value))
                ->addViolation()
            ;
        }
    }
}