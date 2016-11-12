<?php
// src/Sdz/BlogBundle/Controller/BlogController.php
namespace Sdz\BlogBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;



class BlogController extends Controller
{
    public function menuAction($nombre)
    {

        $liste = array(
            array('id' => 1, 'titre' => 'Apprendre Symfony3'),
            array('id' => 2, 'titre' => 'Apprendre Laravel5'),
            array('id' => 3, 'titre' => 'Apprendre CakePHP')
        );


       return $this->render('SdzBlogBundle:Blog:menu.html.twig', array(
           'liste_articles' => $liste
       ));
    }


    public function indexAction($page)
    {
        if( $page < 0 ) {
            throw $this->createNotFoundException('Page inexistante (page = '.$page.')');
        }

        $articles = array(
            array(
                'titre' => 'Apprendre Symfony3',
                'id' => 1,
                'auteur' => 'Selmi',
                'contenu' => 'Apprendre Symfony3 Framework PHP',
                'date' => new \Datetime()),
            array(
                'titre' => 'Apprendre Laravel5',
                'id' => 2,
                'auteur' => 'Selmi',
                'contenu' => 'Apprendre Laravel5 Framework PHP',
                'date' => new \Datetime()),
            array(
                'titre' => 'Apprendre CakePHP',
                'id' => 3,
                'auteur' => 'Selmi',
                'contenu' => 'Apprendre CakePHP Framework PHP',
                'date' => new \Datetime())
        );

        return $this->render('SdzBlogBundle:Blog:index.html.twig', array('articles' => $articles));

    }

    public function viewAction($id)
    {
        $article = array(
            'titre' => 'Apprendre Symfony3',
            'id' => 1,
            'auteur' => 'Selmi',
            'contenu' => 'Apprendre Symfony3 Framework PHP',
            'date' => new \Datetime()
        );
        return $this->render('SdzBlogBundle:Blog:view.html.twig', array( 'article' => $article ));
    }

    public function addAction()
    {
        $request =   $this->container->get('request_stack')->getCurrentRequest();

           if( $request->getMethod() == 'POST' ){
                // Ici, on s'occupera de la création et de la gestion du formulaire
                $this->get('session')->getFlashBag()->add('notice', 'Article bien enregistré');

                // Puis on redirige vers la page de visualisation de cet article
                return $this->redirect( $this->generateUrl('sdzblog_view', array('id' => 5)) );
           }
        // Si on n'est pas en POST, alors on affiche le formulaire
        return $this->render('SdzBlogBundle:Blog:add.html.twig');
    }

    public function updateAction($id)
    {
        $article = array(
            'titre' => 'Apprendre Symfony3',
            'id' => 1,
            'auteur' => 'Selmi',
            'contenu' => 'Apprendre Symfony3 Framework PHP',
            'date' => new \Datetime()
        );

        return $this->render('SdzBlogBundle:Blog:update.html.twig', array('article' => $article));
    }

    public function deleteAction($id)
    {
        $article = array(
            'titre' => 'Apprendre Symfony3',
            'id' => 1,
            'auteur' => 'Selmi',
            'contenu' => 'Apprendre Symfony3 Framework PHP',
            'date' => new \Datetime()
        );

        return $this->render('SdzBlogBundle:Blog:delete.html.twig', array('article' => $article));
    }


    public function viewSlugAction($slug, $annee, $format)
    {
        return new Response("On pourrait afficher l'article correspondant au slug '".$slug."', créé en ".$annee." et au format ".$format.".");
    }


// Symfony Services
    public function sendEmailAction()
    {
        $contenu = $this->render('SdzBlogBundle:Blog:email.txt.twig', array('pseudo' => 'Selmi'));
        // Pour que l'envoi d'e-mail fonctionne, n'oubliez pas de configurer vos paramètres dans app/config/parameters.yml

        // Récupération du service mailer
        $mailer = $this->get('mailer');

        // Création de l'e-mail : le service mailer utilise SwiftMailer, donc nous créons une instance de Swift_Message
        $message = \Swift_Message::newInstance()
            ->setSubject('Hello!')
            ->setFrom('abc@gmail.com')
            ->setTo('xyz@gmail.com')
            ->setBody($contenu);

        // Retour au service mailer, nous utilisons sa méthode « send() » pour envoyer notre $message
        $mailer->send($message);

        return new Response('Email bien envoyé');
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

        // On récupère la requête
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
        var_dump($request->attributes->get('id')); // Est équivalent à : $id
        echo '</pre>';

        echo '<br/>';
        echo '<pre>';
        var_dump($request->getMethod()); // Récupérer la méthode de la requête HTTP
        echo '</pre>';


        echo '<br/>';
        echo '<pre>';
        var_dump($request->isXmlHttpRequest()); // C'est une requête AJAX, retournons du JSON, par exemple
        echo '</pre>';


        echo '<br/>';
        echo '<pre>';
        var_dump($request->getSession()->get('user_id')); // C'est une requête AJAX, retournons du JSON, par exemple
        echo '</pre>';

        // On crée la réponse sans lui donner de contenu pour le moment
       // $response = new Response;

        // On définit le contenu
       // $response->setContent('Ceci est une page d\'erreur 404');

        // On définit le code HTTP
       // $response->setStatusCode(404);
        // On retourne la réponse

     //   return $response;

        return new Response("Affichage de l'article d'id : ".$id.", avec le tag : ".$tag);

    }

    public function sessionAction()
    {
       // Récupération du service session
        $session = $this->get('session');
       // On récupère le contenu de la variable user_id
        $user_id = $session->get('user_id');

       // On définit une nouvelle valeur pour cette variable user_id
        $session->set('user_id', 90);

        return new Response('User ID in Session: '. $user_id);
    }

    public function flashAction()
    {

        $this->get('session')->getFlashBag()->add('info', 'Article bien enregistré');
        // Le « flashBag » est ce qui contient les messages flash dans la session
        // Il peut bien sûr contenir plusieurs messages :
        $this->get('session')->getFlashBag()->add('info', 'Oui oui, il est bien enregistré !');

        // Puis on redirige vers la page de visualisation de cet article
        return $this->redirect( $this->generateUrl('sdzblog_display_flash', array('id' => 5)) );
    }

    public function displayFlashAction($id)
    {
        return $this->render('SdzBlogBundle:Blog:flash.html.twig', array(
            'id' => $id
        ));
    }

}
