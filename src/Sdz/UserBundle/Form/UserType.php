<?php

namespace Sdz\UserBundle\Form;

use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('username', TextType::class)
                 ->add('password', PasswordType::class)
                 ->add('email', EmailType::class)
                 ->add('enabled', CheckboxType::class, array('required' => false))
                ->add('roles', ChoiceType::class, array(
                    'choices' => array(
                        'Super Admin'  => 'ROLE_SUPER_ADMIN',
                        'Admin' => 'ROLE_ADMIN',
                        'Moderateur'   => 'ROLE_MODERATEUR',
                        'Auteur'  => 'ROLE_AUTEUR',
                        'User'  => 'ROLE_USER'
                    ),
                    'required' => true,
                    'multiple'=> true,
                    'placeholder' => 'Choose your gender',
                    'empty_data'  => null
                ))
               ->add('save',      SubmitType::class);
    }


    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Sdz\UserBundle\Entity\User'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'sdz_userbundle_user';
    }


}
