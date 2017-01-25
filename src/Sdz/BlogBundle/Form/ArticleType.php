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
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Sdz\BlogBundle\Repository\CategoryRepository;

use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class ArticleType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
     //   $builder->add('date')->add('title')->add('author')->add('content')->add('published')->add('dateCreated')->add('dateUpdated')->add('nbComments')->add('slug')->add('image')->add('categories');

        // Arbitrairement, on r�cup�re toutes les cat�gories qui commencent par "D"
        $pattern = 'D%';

        $builder
            ->add('date',      DateType::class)
            ->add('title',     TextType::class)
            ->add('author',    TextType::class)
            ->add('content', CkeditorType::class)
           // ->add('content',TextareaType::class, array('attr' => array('class' => 'ckeditor')))
           // ->add('published', CheckboxType::class, array('required' => false))
            ->add('image',     ImageType::class)
            /*
             * Rappel :
             ** - 1er argument : nom du champ, ici � categories �, car c'est le nom de l'attribut
             ** - 2e argument : type du champ, ici � CollectionType � qui est une liste de quelque chose
             ** - 3e argument : tableau d'options du champ
            */
           /* ->add('categories', CollectionType::class, array(
                'entry_type'   => CategoryType::class,
                'allow_add'    => true,
                'allow_delete' => true
            ))*/

            ->add('categories', EntityType::class, array(
                    'class' => 'SdzBlogBundle:Category',
                    'choice_label' => 'name',
                    'multiple' => true,
                    'expanded' => false,
                   /* 'query_builder' => function(CategoryRepository $repository) use($pattern) {
                        return $repository->getLikeQueryBuilder($pattern);
                    }*/
                )
            )
          /*
            // Dans un XxxType
            ->add('article', EntityType::class, array(
                    'class' => 'SdzBlogBundle:Article',
                    'choice_label' => 'title',
                    'query_builder' =>
                        function(\Sdz\BlogBundle\Repository\ArticleRepository $r) {
                            return $r->getSelectList();
                        }
            ))
          */

            ->add('save',      SubmitType::class);

        $builder->addEventListener(
                FormEvents::PRE_SET_DATA,    // 1er argument : L'�v�nement qui nous int�resse : ici, PRE_SET_DATA
                function(FormEvent $event) { // 2e argument : La fonction � ex�cuter lorsque l'�v�nement est d�clench�
                    // On r�cup�re notre objet Article sous-jacent
                    $article = $event->getData();

                    // Cette condition est importante, on en reparle plus loin
                    if (null === $article) {
                        return; // On sort de la fonction sans rien faire lorsque $article vaut null
                    }

                    // Si l'annonce n'est pas publi�e, ou si elle n'existe pas encore en base (id est null)
                    if (!$article->getPublished() || null === $article->getId()) {
                        // Alors on ajoute le champ published
                        $event->getForm()->add('published', CheckboxType::class, array('required' => false));
                    } else {
                        // Sinon, on le supprime
                        $event->getForm()->remove('published');
                    }
                }
       );
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
