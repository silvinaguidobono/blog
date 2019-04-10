<?php

namespace App\Controller;

use App\Form\PostType;
use Doctrine\ORM\Mapping\Id;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Post;

class PostController extends AbstractController
{
    /**
     * @Route("/posts", name="app_posts")
     */
    public function listPosts()
    {
        $user=$this->getUser();
        $posts=$this->getDoctrine()->getRepository(Post::class)->findBy(array('user'=>$user));
        //$posts=$this->getDoctrine()->getRepository(Post::class)->findAll();
        //return $this->render('post/index.html.twig',['controller_name'=>"PostController"]);
        return $this->render('post/index.html.twig',['posts'=>$posts,'user'=>$user]);
    }

    /**
     * @Route("/post/{id}/view", name="app_post_view")
     */
    public function viewPost($id){
        $post=$this->getDoctrine()->getRepository(Post::class)->findBy(array('id'=>$id));
        return $this->render('post/viewPost.html.twig',['post'=>$post[0]]);
    }

    /**
     * @Route("/post/new", name="app_post_new")
     */
    public function newPost(Request $request){
        // crea nuevo objeto Post
        $post=new Post();
        // Rescato usuario logueado
        $user=$this->getUser();
        $post->setUser($user);
        // Asigno username del usuario al autor del post
        $autor=$user->getUsername();
        $post->setAuthor($autor);
        // Rescata fecha de creación del post
        $createdAt = $post->getCreatedAt();

        // crear formulario
        $form=$this->createForm(PostType::class,$post);

        // handle the request
        $form->handleRequest($request);
        $error=$form->getErrors();

        if($form->isSubmitted() && $form->isValid()){
            // data capture
            $post=$form->getData();
            // Si marco Publicar, completo fecha de publicado
            if($post->getisPublished()){
                $post->setPublishedAt($createdAt);
            }
            // handle the entities
            $entityManager=$this->getDoctrine()->getManager();
            $entityManager->persist($post);
            $entityManager->flush();
            $this->addFlash('success', 'Post insertado correctamente');
            return $this->redirectToRoute('app_homepage');
        }
        // render the form
        return $this->render('post/addPost.html.twig',[
            'error'=>$error,
            'form'=>$form->createView()
        ]);
    }

    /**
     * @Route("/post/{id}/edit", name="app_post_edit")
     */
    public function editPost($id,Request $request){
        $post=$this->getDoctrine()->getRepository(Post::class)->findBy(array('id'=>$id));
        $postSel=$post[0];
        $form=$this->createForm(PostType::class,$postSel);

        $form->handleRequest($request);
        $error=$form->getErrors();

        if($form->isSubmitted() && $form->isValid()){
            // Actualiza fecha de modificación del post
            $fecha_actual = new \DateTime();
            $postSel->setModifiedAt($fecha_actual);
            // Actualiza fecha de publicación del post no publicado, si marca Publicar
            if($postSel->getPublishedAt() == null && $postSel->getisPublished()){
                $postSel->setPublishedAt($fecha_actual);
            }
            // handle the entities
            $entityManager=$this->getDoctrine()->getManager();
            //$entityManager->persist($postSel);
            $entityManager->flush();
            $this->addFlash('success', 'Post modificado correctamente');
            return $this->redirectToRoute('app_homepage');
        }

        // render the form
        return $this->render('post/editPost.html.twig',[
            'error'=>$error,
            'form'=>$form->createView()
        ]);

    }

    /**
     * @Route("/post/{id}/delete", name="app_post_delete")
     */
    public function deletePost($id, Request $request){
        $post=$this->getDoctrine()->getRepository(Post::class)->findBy(array('id'=>$id));
        $postSel=$post[0];
        $entityManager=$this->getDoctrine()->getManager();
        $entityManager->remove($postSel);
        $entityManager->flush();
        $this->addFlash('success', 'Post eliminado correctamente');
        return $this->redirectToRoute('app_homepage');
    }

}
