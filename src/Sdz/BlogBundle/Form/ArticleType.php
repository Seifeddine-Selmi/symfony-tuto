<?php

namespace Sdz\BlogBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ArticleType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
     //   $builder->add('date')->add('title')->add('author')->add('content')->add('published')->add('dateCreated')->add('dateUpdated')->add('nbComments')->add('slug')->add('image')->add('categories');
        $builder
            ->add('date',      DateType::class)
            ->add('title',     TextType::class)
            ->add('author',    TextType::class)
            ->add('content',   TextareaType::class)
            ->add('published', CheckboxType::class, array('required' => false))
            ->add('image',     ImageType::class)
            /*
             * Rappel :
             ** - 1er argument : nom du champ, ici � categories �, car c'est le nom de l'attribut
             ** - 2e argument : type du champ, ici � CollectionType � qui est une liste de quelque chose
             ** - 3e argument : tableau d'options du champ
            */
            ->add('categories', CollectionType::class, array(
                'entry_type'   => CategoryType::class,
                'allow_add'    => true,
                'allow_delete' => true
            ))
            ->add('save',      SubmitType::class);
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Sdz\BlogBundle\Entity\Article'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'sdz_blogbundle_article';
    }


}
