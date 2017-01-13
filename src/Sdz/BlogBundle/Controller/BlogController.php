<?php
// src/Sdz/BlogBundle/Controller/BlogController.php
namespace Sdz\BlogBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sdz\BlogBundle\Entity\Article;
use Sdz\BlogBundle\Entity\Image;
use Sdz\BlogBundle\Entity\Comment;
use Sdz\BlogBundle\Entity\ArticleCompetence;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Sdz\BlogBundle\Form\ArticleType;
use Sdz\BlogBundle\Form\ArticleEditType;
class BlogController extends Controller
{

    public function indexAction($page)
    {

        $articles = $this->getDoctrine()
                         ->getManager()
                         ->getRepository('SdzBlogBundle:Article')
                         ->getArticles(3, $page);


        return $this->render('SdzBlogBundle:Blog:index.html.twig',
            array(
                'articles' => $articles,
                'page' => $page,
                'nbPage' => ceil(count($articles)/3)
            ));

    }

    public function viewAction(Article $article)
    {

        $em = $this->getDoctrine()
                   ->getManager();

        $articleCompetence_list = $em->getRepository('SdzBlogBundle:ArticleCompetence')
                                     ->findByArticle($article->getId());

        return $this->render('SdzBlogBundle:Blog:view.html.twig',
            array( 'article' => $article,
                'articleCompetence_list' =>  $articleCompetence_list
            ));
    }

    public function addAction(Request $request)
    {

        $article = new Article();
        $article->setDate(new \Datetime());

        /*
      //  $formBuilder = $this->createFormBuilder($article); //S2

        // On crée le FormBuilder grâce au service form factory
         $formBuilder = $this->get('form.factory')->createBuilder(FormType::class, $article); //S3

        $formBuilder
            ->add('date', DateType::class)
            ->add('title', TextType::class)
            ->add('content', TextareaType::class)
            ->add('author', TextType::class)
            ->add('published', CheckboxType::class, array('required' => false))
            ->add('save',      SubmitType::class);

        $form = $formBuilder->getForm();
      */

       // $form = $this->createForm(new ArticleType, $article); //S2
       // $form = $this->get('form.factory')->create(ArticleType::class, $article); // S3
       $form = $this->createForm(ArticleType::class, $article); // S3

       if( $request->isMethod('POST') ){

           // $form->bind($request); //S2
           $form->handleRequest($request); //S3

           if ($form->isValid()) {
               // c'est elle qui déplace l'image là où on veut les stocker
               // $article->getImage()->upload();

                $em = $this->getDoctrine()->getManager();
                $em->persist($article);
                // $em->persist($article->getImage()); // if cascade={"persist"} not exists
                $em->flush();

               $request->getSession()->getFlashBag()->add('notice', 'Article bien enregistrÃ©');
              // return $this->redirect( $this->generateUrl('sdzblog_view', array('id' => $article->getId())) ); //S2
              return $this->redirectToRoute('sdzblog_view', array('id' => $article->getId())); //S3

         }
       }
        return $this->render('SdzBlogBundle:Blog:add.html.twig',
            array(
                'form' => $form->createView(),
            ));


    }

    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()
                   ->getManager();

        $article = $em->getRepository('SdzBlogBundle:Article')
                      ->find($id);

        if ($article === null) {
           // throw $this->createNotFoundException('Article[id='.$id.'] inexistant.'); //S2
            throw new NotFoundHttpException("L'article d'id ".$id." n'existe pas."); //S3

        }

        $form = $this->createForm(ArticleEditType::class, $article); // S3
        if( $request->isMethod('POST') ){

            // $form->bind($request); //S2
            $form->handleRequest($request); //S3

            if ($form->isValid()) {

                $em = $this->getDoctrine()->getManager();
               // $em->persist($article); // Inutile de persister ici, Doctrine connait déjà notre article
                // $em->persist($article->getImage()); // if cascade={"persist"} not exists
                $em->flush();

                $request->getSession()->getFlashBag()->add('notice', 'Article bien modifiéÃ©');
                return $this->redirectToRoute('sdzblog_view', array('id' => $article->getId())); //S3

            }
        }

        return $this->render('SdzBlogBundle:Blog:update.html.twig',
            array(
                'article' => $article,
                'form' => $form->createView()
            )
        );
    }

    public function deleteAction(Request $request, $id)
    {

        $em = $this->getDoctrine()
                   ->getManager();

        $article = $em->getRepository('SdzBlogBundle:Article')
                      ->find($id);

        if ($article === null) {
           // throw $this->createNotFoundException('Article[id='.$id.'] inexistant.'); //S2
            throw new NotFoundHttpException("L'article d'id ".$id." n'existe pas."); //S3
        }


        // On crée un formulaire vide, qui ne contiendra que le champ CSRF
        // Cela permet de protéger la suppression d'annonce contre cette faille
        $form = $this->get('form.factory')->create();
      //  $form = $this->createFormBuilder()->getForm(); //S2

        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {

            $comments_list = $em->getRepository('SdzBlogBundle:Comment')->findByArticle($article->getId());
            foreach($comments_list as $comment) {
                $em->remove($comment);
            }

            $articleCompetence_list = $em->getRepository('SdzBlogBundle:ArticleCompetence')->findByArticle($article->getId());
            foreach($articleCompetence_list as $competence) {
                $em->remove($competence);
            }

            $em->remove($article);
            $em->flush();


            $request->getSession()->getFlashBag()->add('notice', 'Article bien supprimÃ©');


            return $this->redirect( $this->generateUrl('sdzblog_home') );
        }


        return $this->render('SdzBlogBundle:Blog:delete.html.twig',
            array(
                'article' => $article,
                'form' => $form->createView()
            )
        );
    }

    public function menuAction($nombre)
    {

        $em = $this->getDoctrine()
                   ->getManager();

        $articles = $em->getRepository('SdzBlogBundle:Article')
                        ->findBy(
                                array(),
                                array('date' => 'desc'),
                                $nombre,
                                0
                            );


        return $this->render('SdzBlogBundle:Blog:menu.html.twig',
            array(
              'liste_articles' => $articles
            ));
    }





    /********************************** Test ****************************************************/

    public function menuTestAction($nombre)
    {

        $articles = array(
            array('id' => 1, 'title' => 'Apprendre Symfony3'),
            array('id' => 2, 'title' => 'Apprendre Laravel5'),
            array('id' => 3, 'title' => 'Apprendre CakePHP')
        );

        $em = $this->getDoctrine()->getManager();
        $articles = $em->getRepository('SdzBlogBundle:Article')->findAll();


        return $this->render('SdzBlogBundle:Blog:menu.html.twig', array(
            'liste_articles' => $articles
        ));
    }

    public function indexTestAction($page)
    {
        if( $page < 0 ) {
            throw $this->createNotFoundException('Page inexistante (page = '.$page.')');
        }
        $text = "http://localhost:8000/blog";
        // On récupérere le service
        $antispam = $this->container->get('sdz_blog.antispam');

        // Je pars du principe que $text contient le texte d'un message quelconque
        if ($antispam->isSpam($text)) {
            throw new \Exception('Votre message a Ã©tÃ© dÃ©tectÃ© comme spam !');
        }

        $articles = $this->getDoctrine()->getManager()->getRepository('SdzBlogBundle:Article')->findAll();

        return $this->render('SdzBlogBundle:Blog:index.html.twig', array('articles' => $articles));

    }

    public function viewTestAction($id)
    {

        // On récupère l'EntityManager
        $em = $this->getDoctrine()
            ->getManager();

        // On récupère le repository
       /* $repository = $em
            ->getRepository('SdzBlogBundle:Article');

          // On récupère l'entité correspondant à l'id $id
          $article = $repository->find($id);
      */

        $article = $em->find('SdzBlogBundle:Article', $id);

       // $article est donc une instance de Sdz\BlogBundle\Entity\Article
       // Ou null si aucun article n'a été trouvé avec l'id $id
        if($article === null)
        {
          //  throw $this->createNotFoundException('Article[id='.$id.'] inexistant.');
            throw new NotFoundHttpException('Article[id='.$id.'] inexistant.');
        }

        // On récupère la liste des commentaires
        $comments_list = $em->getRepository('SdzBlogBundle:Comment')->findAll();

        // On récupère les articleCompetence pour l'article $article
        $articleCompetence_list = $em->getRepository('SdzBlogBundle:ArticleCompetence')->findByArticle($article->getId());

        return $this->render('SdzBlogBundle:Blog:view.html.twig',
                            array( 'article' => $article,
                                   'comments_list' =>  $comments_list,
                                   'articleCompetence_list' =>  $articleCompetence_list
                             ));
    }

    public function addTest1Action()
    {
        $request =   $this->container->get('request_stack')->getCurrentRequest();

        if( $request->getMethod() == 'POST' ){
            // Ici, on s'occupera de la crÃ©ation et de la gestion du formulaire
            $this->get('session')->getFlashBag()->add('notice', 'Article bien enregistrÃ©');

            // Puis on redirige vers la page de visualisation de cet article
            return $this->redirect( $this->generateUrl('sdzblog_view', array('id' => 5)) );
        }
        // Si on n'est pas en POST, alors on affiche le formulaire
        return $this->render('SdzBlogBundle:Blog:add.html.twig');
    }

    public function addTest2Action()
    {
       // Création de l'entité Article
        $article = new Article;
        $article->setTitle('Article Symfony');
        $article->setAuthor('Selmi');
        $article->setContent("Article Symfony avec des commentaires");


        // Création de l'entité Image
        $image = new Image();
        $image->setUrl('https://symfony.com/images/v5/logos/header-logo.svg');
        $image->setAlt('Logo Symfony');

        // On lie l'image à l'article
        $article->setImage($image);


        // Création d'un premier commentaire
        $comment1 = new Comment();
        $comment1->setAuthor('Selmi');
        $comment1->setContent('On veut les photos !');

        $comment2 = new Comment();
        $comment2->setAuthor('Seif');
        $comment2->setContent('Les photos arrivent !');

        // On lie les commentaires à l'article
        $comment1->setArticle($article);
        $comment2->setArticle($article);

        // On récupère l'EntityManager
        $em = $this->getDoctrine()->getManager();

        // Étape 1 : On « persiste » l'entité: Manipulé des objets
         $em->persist($article);

        // Étape 1 bis : si on n'avait pas défini le cascade={"persist"}, on devrait persister à la main l'entité $image
         $em->persist($image);


         // Pour cette relation pas de cascade, car elle est définie dans l'entité Commentaire et non Article
         // On doit donc tout persister à la main ici
        $em->persist($comment1);
        $em->persist($comment2);

        // Étape 2 : On « flush » tout ce qui a été persisté avant: enregistré l'article en base de données
        $em->flush();


        $request =   $this->container->get('request_stack')->getCurrentRequest();

        if( $request->getMethod() == 'POST' ){
            // Ici, on s'occupera de la création et de la gestion du formulaire
            $this->get('session')->getFlashBag()->add('info', 'Article bien enregistrÃ©');

            // Puis on redirige vers la page de visualisation de cet article
           // return $this->redirect( $this->generateUrl('sdzblog_view', array('id' => $article->getId())) );
            return $this->redirectToRoute('sdzblog_view', array('id' => $article->getId()));
        }
        return $this->render('SdzBlogBundle:Blog:add.html.twig');
    }

    public function addTestAction()
    {
        // On récupère l'EntityManager
        $em = $this->getDoctrine()->getManager();

        // Création de l'entité Article
        $article = new Article;
        $article->setTitle('Article Symfony avec competence');
        $article->setAuthor('Selmi');
        $article->setContent("Article Symfony avec competences");



        // Dans ce cas, on doit créer effectivement l'article en bdd pour lui assigner un id
        // On doit faire cela pour pouvoir enregistrer les ArticleCompetence par la suite
        $em->persist($article);


        // Création d'un premier commentaire
        $comment = new Comment();
        $comment->setAuthor('Selmi');
        $comment->setContent('Comment Symfony !');



        // On lie les commentaires à l'article
        $comment->setArticle($article);


        $em->persist($comment);


        $em->flush(); // Maintenant, $article a un id défini



        // Les compétences existent déjà, on les récupère depuis la bdd
        $competences_list = $em->getRepository('SdzBlogBundle:Competence')->findAll(); // Pour l'exemple, notre Article contient toutes les Competences

        // Pour chaque compétence
        foreach($competences_list as $i => $competence)
        {
           // On crée une nouvelle « relation entre 1 article et 1 compétence »
           $articleCompetence[$i] = new ArticleCompetence;

          // On la lie à l'article, qui est ici toujours le même
          $articleCompetence[$i]->setArticle($article);

         // On la lie à la compétence, qui change ici dans la boucle foreach
          $articleCompetence[$i]->setCompetence($competence);

         // Arbitrairement, on dit que chaque compétence est requise au niveau 'Expert'
          $articleCompetence[$i]->setLevel('Expert');

         // Et bien sûr, on persiste cette entité de relation, propriétaire des deux autres relations
         $em->persist($articleCompetence[$i]);
       }
         // On déclenche l'enregistrement
        $em->flush();

        $request =   $this->container->get('request_stack')->getCurrentRequest();
        if( $request->getMethod() == 'POST' ){
            // Ici, on s'occupera de la création et de la gestion du formulaire
            $this->get('session')->getFlashBag()->add('info', 'Article bien enregistrÃ©');

            // Puis on redirige vers la page de visualisation de cet article
            // return $this->redirect( $this->generateUrl('sdzblog_view', array('id' => $article->getId())) );
            return $this->redirectToRoute('sdzblog_view', array('id' => $article->getId()));
        }
        return $this->render('SdzBlogBundle:Blog:add.html.twig');

    }

    public function updateTestAction($id)
    {
        // On récupère l'EntityManager
          $em = $this->getDoctrine()->getManager();

            // On récupère l'entité correspondant à l'id $id
            $article = $em->getRepository('SdzBlogBundle:Article')->find($id);

            if ($article === null) {
                throw $this->createNotFoundException('Article[id='.$id.'] inexistant.');
            }

            // On récupère toutes les catégories :
            $categories_list = $em->getRepository('SdzBlogBundle:Category')->findAll();

            // On boucle sur les catégories pour les lier à l'article
            foreach($categories_list as $category)
            {
                $article->addCategory($category);
            }

            // Inutile de persister l'article, on l'a récupéré avec Doctrine
            // Étape 2 : On déclenche l'enregistrement
          $em->flush();

        return $this->render('SdzBlogBundle:Blog:update.html.twig', array('article' => $article));
    }

    public function deleteTestAction($id)
    {
        // On récupère l'EntityManager
        $em = $this->getDoctrine()->getManager();

        // On récupère l'entité correspondant à l'id $id
        $article = $em->getRepository('SdzBlogBundle:Article')->find($id);

        if ($article === null) {
            throw $this->createNotFoundException('Article[id='.$id.'] inexistant.');
        }

        // On récupère toutes les catégories :
        $categories_list = $em->getRepository('SdzBlogBundle:Category')->findAll();


        // On enlève toutes ces catégories de l'article
        foreach($categories_list as $category)
        {
            // On fait appel à la méthode removeCategory() dont on a parlé plus haut
            // Attention ici, $categorie est bien une instance de Categorie, et pas seulement un id
            $article->removeCategory($category);
        }

        // On n'a pas modifié les catégories : inutile de les persister
        // On a modifié la relation Article - Categorie
        // Il faudrait persister l'entité propriétaire pour persister la relation
        // Or l'article a été récupéré depuis Doctrine, inutile de le persister
        // On déclenche la modification
       $em->flush();


        return $this->render('SdzBlogBundle:Blog:delete.html.twig', array('article' => $article));
    }


    public function viewSlugAction($slug, $annee, $format)
    {
        return new Response("On pourrait afficher l'article correspondant au slug '".$slug."', crÃ©Ã© en ".$annee." et au format ".$format.".");
    }


    public function updateImageAction($id_article)
    {
        $em = $this->getDoctrine()->getManager();
        // On récupère l'article
        $article = $em->getRepository('SdzBlogBundle:Article')->find($id_article);
        // On modifie l'URL de l'image par exemple
        $article->getImage()->setUrl('test.png');
        // On n'a pas besoin de persister notre article (si vous le faites, aucune erreur n'est déclenchée, Doctrine l'ignore)
        // Rappelez-vous, il l'est automatiquement car on l'a récupéré depuis Doctrine
        // Pas non plus besoin de persister l'image ici, car elle est également récupérée par Doctrine
        // On déclenche la modification
        $em->flush();
        return new Response('OK');
}

// Symfony Services
    public function sendEmailAction()
    {
        $contenu = $this->render('SdzBlogBundle:Blog:email.txt.twig', array('pseudo' => 'Selmi'));
        // Pour que l'envoi d'e-mail fonctionne, n'oubliez pas de configurer vos paramÃ¨tres dans app/config/parameters.yml

        // RÃ©cupÃ©ration du service mailer
        $mailer = $this->get('mailer');

        // CrÃ©ation de l'e-mail : le service mailer utilise SwiftMailer, donc nous crÃ©ons une instance de Swift_Message
        $message = \Swift_Message::newInstance()
            ->setSubject('Hello!')
            ->setFrom('abc@gmail.com')
            ->setTo('xyz@gmail.com')
            ->setBody($contenu);

        // Retour au service mailer, nous utilisons sa mÃ©thode Â« send() Â» pour envoyer notre $message
        $mailer->send($message);

        return new Response('Email bien envoyÃ©');
    }

    public function templatingAction()
    {

        $templating = $this->get('templating');

        $contenu = $templating->render('SdzBlogBundle:Blog:index.html.twig', array('nom' => 'Selmi'));

        return new Response($contenu);
    }

    public function requestAction($id)
    {

       // http://localhost:8000/blog/request/9?tag=vacances

        // On rÃ©cupÃ¨re la requÃªte
        $request =   $this->container->get('request_stack')->getCurrentRequest();

        $id = $request->attributes->get('id'); // 9
        $tag = $request->query->get('tag');   // vacances   // $_GET

        echo '<br/>';
        echo '<pre>';
        var_dump($request->request->get('tag'));        // $_POST
        echo '</pre>';

        echo '<br/>';
        echo '<pre>';
        var_dump($request->cookies->get('tag'));        // $_COOKIE
        echo '</pre>';

        echo '<br/>';
        echo '<pre>';
        var_dump($request->server->get('REQUEST_URI')); // $_SERVER
        echo '</pre>';

        echo '<br/>';
        echo '<pre>';
        var_dump($request->headers->get('USER_AGENT')); // $_SERVER['HTTP_*']
        echo '</pre>';

        echo '<br/>';
        echo '<pre>';
        var_dump($request->attributes->get('id')); // Est Ã©quivalent Ã  : $id
        echo '</pre>';

        echo '<br/>';
        echo '<pre>';
        var_dump($request->getMethod()); // RÃ©cupÃ©rer la mÃ©thode de la requÃªte HTTP
        echo '</pre>';


        echo '<br/>';
        echo '<pre>';
        var_dump($request->isXmlHttpRequest()); // C'est une requÃªte AJAX, retournons du JSON, par exemple
        echo '</pre>';


        echo '<br/>';
        echo '<pre>';
        var_dump($request->getSession()->get('user_id')); // C'est une requÃªte AJAX, retournons du JSON, par exemple
        echo '</pre>';

        // On crÃ©e la rÃ©ponse sans lui donner de contenu pour le moment
       // $response = new Response;

        // On dÃ©finit le contenu
       // $response->setContent('Ceci est une page d\'erreur 404');

        // On dÃ©finit le code HTTP
       // $response->setStatusCode(404);
        // On retourne la rÃ©ponse

     //   return $response;

        return new Response("Affichage de l'article d'id : ".$id.", avec le tag : ".$tag);

    }

    public function sessionAction()
    {
       // RÃ©cupÃ©ration du service session
        $session = $this->get('session');
       // On rÃ©cupÃ¨re le contenu de la variable user_id
        $user_id = $session->get('user_id');

       // On dÃ©finit une nouvelle valeur pour cette variable user_id
        $session->set('user_id', 90);

        return new Response('User ID in Session: '. $user_id);
    }

    public function flashAction()
    {

        $this->get('session')->getFlashBag()->add('info', 'Article bien enregistrÃ©');
        // Le Â« flashBag Â» est ce qui contient les messages flash dans la session
        // Il peut bien sÃ»r contenir plusieurs messages :
        $this->get('session')->getFlashBag()->add('info', 'Oui oui, il est bien enregistrÃ© !');

        // Puis on redirige vers la page de visualisation de cet article
        return $this->redirect( $this->generateUrl('sdzblog_display_flash', array('id' => 5)) );
    }

    public function displayFlashAction($id)
    {
        return $this->render('SdzBlogBundle:Blog:flash.html.twig', array(
            'id' => $id
        ));
    }


    public function testAction()
    {
        /*
        $article = new Article;
        $article->setDate(new \Datetime()); // date d'aujourd'hui
        $article->setId(1);
        $article->setTitle('Mon dernier weekend');
        $article->setAuthor('Selmi');
        $article->setContent("C'était vraiment super et on s'est bien amusé.");
       // return $this->render('SdzBlogBundle:Article:test.html.twig', array('article' => $article));
        return $this->render('SdzBlogBundle:Blog:test.html.twig', array('article' => $article));
        */

/*
        $listeArticles  = $this->getDoctrine()
            ->getManager()
            ->getRepository('SdzBlogBundle:Article')
            ->myFindOne(17);

        $listeArticles  = $this->getDoctrine()
            ->getManager()
            ->getRepository('SdzBlogBundle:Article')
            ->findByAuthorAndDate('Selmi', new \Datetime());





        $listeArticles  = $this->getDoctrine()
            ->getManager()
            ->getRepository('SdzBlogBundle:Article')
          //  ->myFind();
          //  ->myFindAll();
          //  ->myFindAllDQL();
           ->getArticleAndComments();

        foreach($listeArticles as $article)
        {
            // $article est une instance de Article
            // echo $article->getContent();


            foreach($article->getComments() as $comment)
            {
           //     echo $comment->getContent();
            }
            // echo $article['content'];
        }



        $categories = array('Doctrine2');
        $listeArticlesAndCategories  = $this->getDoctrine()
            ->getManager()
            ->getRepository('SdzBlogBundle:Article')
           // ->getArticleAndCategories();
            ->getAndCategories($categories);

        foreach($listeArticlesAndCategories as $article)
        {
            // $article est une instance de Article
            // echo $article->getContent();


            foreach($article->getCategories() as $category)
            {
                echo $category->getName();
            }
            // echo $article['content'];
        }

        $article  = $this->getDoctrine()
            ->getManager()
            ->getRepository('SdzBlogBundle:Article')
            //  ->myFindOne(17);
            ->myFindDQL(20);
         $article->setTitle('Article Symfony 4');

        $em = $this->getDoctrine()->getManager();
        $em->persist($article);

        $em->flush(); // Maintenant, $article a un id défini

        die;
*/

        $article = new Article();
        $article->setTitle("L'histoire d'un bon weekend !");
        $article->setAuthor("Selmi");
        $article->setContent("Contenu ...");
        $em = $this->getDoctrine()->getManager();
        $em->persist($article);
        $em->flush(); // C'est à ce moment qu'est généré le slug
        return new Response('Slug généré : '.$article->getSlug()); //Affiche « Slug généré : l-histoire-d-un-bon-weekend »

    }

}
