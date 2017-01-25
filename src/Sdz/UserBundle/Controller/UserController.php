<?php

namespace Sdz\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sdz\UserBundle\Entity\User;
use Sdz\UserBundle\Form\UserType;
use Sdz\UserBundle\Form\UserEditType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

class UserController extends Controller
{

    public function indexAction()
    {

        // Pour récupérer le service UserManager du bundle
        $userManager = $this->get('fos_user.user_manager');

        // Pour récupérer la liste de tous les utilisateurs
        $users = $userManager->findUsers();

        return $this->render('SdzUserBundle:User:index.html.twig',
            array(
                'users' => $users,
            ));

    }


    public function viewAction($id)
    {
        $userManager = $this->get('fos_user.user_manager');

        $user = $userManager->findUserBy(array('id' => $id));



        return $this->render('SdzUserBundle:User:view.html.twig',
            array( 'user' => $user,
            ));
    }

    public function addAction(Request $request)
    {

        $user = new User();
        $userManager = $this->get('fos_user.user_manager');
        $password = $request->get('sdz_userbundle_user')['password'];
        $user->setPlainPassword($password);
        $user->setLastLogin(new \Datetime());
        $user->setEnabled(true);


        $form = $this->createForm(UserType::class, $user); // S3

        if( $request->isMethod('POST') ){


            $form->handleRequest($request); //S3

            if ($form->isValid()) {

                $exists = $userManager->findUserBy(array('email' => $user->getEmail()));
                if ($exists instanceof User) {
                    throw new HttpException(409, 'Email already taken');
                }

                $userManager->updateUser($user);

                $request->getSession()->getFlashBag()->add('notice', 'User bien enregistrÃ©');

               return $this->redirectToRoute('sdz_user_view', array('id' => $user->getId())); //S3

            }
        }


        return $this->render('SdzUserBundle:User:add.html.twig',
            array( 'user' => $user,
                'form' => $form->createView()
            ));

    }

    /**
     * @Security("has_role('ROLE_SUPER_ADMIN')")
     */
    public function editAction(Request $request, $id)
    {


        $userManager = $this->get('fos_user.user_manager');

        $user = $userManager->findUserBy(array('id' => $id));

        if ($user === null) {

            throw new NotFoundHttpException("Utilisateur d'id ".$id." n'existe pas."); //S3

        }

        $form = $this->createForm(UserEditType::class, $user); // S3

        if( $request->isMethod('POST') ){


            $form->handleRequest($request); //S3

            if ($form->isValid()) {


                $password = $request->get('sdz_userbundle_user')['password'];

                $user->setPlainPassword($password);

                $userManager->updateUser($user);

                $request->getSession()->getFlashBag()->add('notice', 'User bien modifiéÃ©');
                return $this->redirectToRoute('sdz_user_view', array('id' => $user->getId())); //S3

            }
        }

        return $this->render('SdzUserBundle:User:edit.html.twig',
            array( 'user' => $user,
                   'form' => $form->createView()
            ));

    }

    /**
     * @Security("has_role('ROLE_SUPER_ADMIN')")
     */
    public function deleteAction(Request $request, $id)
    {

        $userManager = $this->get('fos_user.user_manager');

        $user = $userManager->findUserBy(array('id' => $id));

        if ($user === null) {

            throw new NotFoundHttpException("Utilisateur d'id ".$id." n'existe pas."); //S3

        }

        $form = $this->get('form.factory')->create();
        if( $request->isMethod('POST') ){


            $form->handleRequest($request); //S3

            if ($form->isValid()) {

                $userManager->deleteUser($user);

                $request->getSession()->getFlashBag()->add('notice', 'User bien supprimÃ©');
                return $this->redirectToRoute('sdz_user_home'); //S3

            }
        }


        return $this->render('SdzUserBundle:User:delete.html.twig',
            array( 'user' => $user,
                'form' => $form->createView()
            ));

    }




}
