<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Form\CommentType;
use App\Entity\Post;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


class CommentController extends AbstractController
{
    /**
     * @Route("/comment", name="comment")
     */
    public function index()
    {
        return $this->render('comment/index.html.twig', [
            'controller_name' => 'CommentController',
        ]);
    }

    /**
     * @Route("/comment/{id}/new", name="app_comment_new")
     */
    public function newComment($id,Request $request){
        $post=$this->getDoctrine()->getRepository(Post::class)->find($id);
        // crea nuevo objeto Comment
        $comment=new Comment();
        // Rescato usuario logueado
        $user=$this->getUser();
        $comment->setUser($user);
        $comment->setPost($post);

        // crear formulario
        $form=$this->createForm(CommentType::class,$comment);

        // handle the request
        $form->handleRequest($request);
        $error=$form->getErrors();

        if($form->isSubmitted() && $form->isValid()){
            // data capture
            $comment=$form->getData();
            // handle the entities
            $entityManager=$this->getDoctrine()->getManager();
            $entityManager->persist($comment);
            $entityManager->flush();
            $this->addFlash('success', 'Comentario insertado correctamente');
            return $this->redirectToRoute('app_homepage');
        }
        // render the form
        return $this->render('comment/addComment.html.twig',[
            'error'=>$error,
            'post' => $post,
            'form'=>$form->createView()
        ]);
    }

    /**
     * This controller is called directly via the render() function in the
     * post/viewPost.html.twig template. That's why it's not needed to define
     * a route name for it.
     *
     * The "id" of the Post is passed in and then turned into a Post object
     * automatically by the ParamConverter.
     */
    public function commentForm(Post $post): Response
    {
        $form = $this->createForm(CommentType::class);

        return $this->render('comment/addComment.html.twig', [
            'post' => $post,
            'form' => $form->createView(),
        ]);
    }
}
