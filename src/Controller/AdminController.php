<?php

namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use App\Entity\User;
use App\Form\AdminUserType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use App\Entity\Post;
use App\Form\PostType;

/**
 * Class AdminController
 * @package App\Controller
 * @IsGranted("ROLE_ADMIN")
 *
 */
class AdminController extends AbstractController
{
    /**
     * @Route("/admin/posts", name="app_admin_posts")
     */
    public function listPosts()
    {
        $user=$this->getUser();
        $posts=$this->getDoctrine()->getRepository(Post::class)->findAll();
        return $this->render('home/home.html.twig',['posts'=>$posts,'user'=>$user]);
        //return $this->render('post/index.html.twig',['posts'=>$posts]);
    }

    /**
     * @Route("/admin/users", name="app_admin_users")
     */
    public function listUsers()
    {
        $users=$this->getDoctrine()->getRepository(User::class)->findAll();
        return $this->render('admin/users.html.twig',['users'=>$users]);
    }

    /**
     * @Route("/admin/user/{id}/view", name="app_admin_user_view")
     */
    public function viewUser($id){
        $user=$this->getDoctrine()->getRepository(User::class)->findBy(array('id'=>$id));
        return $this->render('admin/userView.html.twig',['user'=>$user[0]]);
    }

    /**
     * @Route("/admin/user/new", name="app_admin_user_new")
     */
    public function addUser(Request $request, UserPasswordEncoderInterface $passwordEncoder){
        $user=new User();
        $form=$this->createForm(AdminUserType::class,$user);

        $form->handleRequest($request);
        $error=$form->getErrors();

        if($form->isSubmitted() && $form->isValid()){
            //
            $user=$form->getData();
            //
            // encrypt the plainpassword
            $password=$passwordEncoder->encodePassword($user,$user->getPlainPassword());
            $user->setPassword($password);
            // handle the entities
            $entityManager=$this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();
            $this->addFlash('success', 'Usuario insertado correctamente');
            return $this->redirectToRoute('app_admin_users');
        }
        // render the form
        return $this->render('admin/addUser.html.twig',[
            'error'=>$error,
            'form'=>$form->createView()
        ]);
    }

    /**
     * @Route("/admin/user/{id}/edit", name="app_admin_user_edit")
     */
    public function editUser($id,Request $request, UserPasswordEncoderInterface $passwordEncoder){
        $user=$this->getDoctrine()->getRepository(User::class)->findBy(array('id'=>$id));
        $userSel=$user[0];
        $form=$this->createForm(AdminUserType::class,$userSel);

        $form->handleRequest($request);
        $error=$form->getErrors();

        if($form->isSubmitted() && $form->isValid()){
            // encrypt the plainpassword
            $password=$passwordEncoder->encodePassword($userSel,$userSel->getPlainPassword());
            $userSel->setPassword($password);
            // handle the entities
            $entityManager=$this->getDoctrine()->getManager();
            //$entityManager->persist($userSel);
            $entityManager->flush();
            $this->addFlash('success', 'Usuario modificado correctamente');
            return $this->redirectToRoute('app_admin_users');
        }

        // render the form
        return $this->render('admin/editUser.html.twig',[
            'error'=>$error,
            'form'=>$form->createView()
        ]);

    }

    /**
     * @Route("/admin/user/{id}/delete", name="app_admin_user_delete")
     */
    public function deleteUser($id, Request $request){
        $user=$this->getDoctrine()->getRepository(User::class)->findBy(array('id'=>$id));
        $userSel=$user[0];

        $entityManager=$this->getDoctrine()->getManager();
        $entityManager->remove($userSel);
        $entityManager->flush();
        $this->addFlash('success', 'Usuario eliminado correctamente');
        return $this->redirectToRoute('app_admin_users');
    }

    /**
     * @Route("/admin/post/new", name="app_admin_post_new")
     */
    public function addPost(Request $request){
        // utilizo esta función para prueba inicial de alta de post
        $post=new Post();
        // Rescato usuario logueado
        $user=$this->getUser();
        $post->setUser($user);
        // Asigno username del usuario al autor del post
        $autor=$user->getUsername();
        $post->setAuthor($autor);

        $form=$this->createForm(PostType::class,$post);

        $form->handleRequest($request);
        $error=$form->getErrors();

        if($form->isSubmitted() && $form->isValid()){
            //
            $post=$form->getData();
            //
            // handle the entities
            $entityManager=$this->getDoctrine()->getManager();
            $entityManager->persist($post);
            $entityManager->flush();
            $this->addFlash('success', 'Post insertado correctamente');
            return $this->redirectToRoute('app_admin_users');
        }
        // render the form
        return $this->render('post/addPost.html.twig',[
            'error'=>$error,
            'form'=>$form->createView()
        ]);
    }
}
