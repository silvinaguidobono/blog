<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Form\PostType;
use Doctrine\ORM\Mapping\Id;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Post;
use App\Entity\Tag;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Knp\Component\Pager\PaginatorInterface;

class PostController extends AbstractController
{
    /**
     * @Route("/posts", name="app_posts")
     */
    public function listPosts()
    {
        $user=$this->getUser();
        $username=$user->getUsername();
        $posts=$this->getDoctrine()->getRepository(Post::class)->findBy(array('user'=>$user));
        //$posts=$this->getDoctrine()->getRepository(Post::class)->findAll();
        //return $this->render('post/index.html.twig',['controller_name'=>"PostController"]);
        return $this->render('post/index.html.twig',['posts'=>$posts,'username'=>$username]);
    }

    /**
     * @Route("/post/{id}/view", name="app_post_view")
     */
    public function viewPost($id){
        // Obtengo el id del usuario logeado
        $user_id=0;
        if(!is_null($this->getUser())){
            $user=$this->getUser();
            $user_id=$user->getId();
        }
        $post=$this->getDoctrine()->getRepository(Post::class)->find($id);
        return $this->render('post/viewPost.html.twig',['post'=>$post,'userId'=>$user_id]);
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
            return $this->redirectToRoute('app_posts');
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
        $post=$this->getDoctrine()->getRepository(Post::class)->find($id);
        // controlo que no se puedan editar posts ajenos salvo que sea administrador.
        $user=$this->getUser();
        if(!(in_array("ROLE_ADMIN",$user->getRoles())) && $user->getId() != $post->getUser()->getId()){
            $this->addFlash('warning', 'No puede editar un post ajeno');
            return $this->redirectToRoute('app_homepage');
        }
        $form=$this->createForm(PostType::class,$post);
        $form->handleRequest($request);
        $error=$form->getErrors();

        if($form->isSubmitted() && $form->isValid()){
            // Actualiza fecha de modificación del post
            $fecha_actual = new \DateTime();
            $post->setModifiedAt($fecha_actual);
            // Actualiza fecha de publicación del post no publicado, si marca Publicar
            if($post->getPublishedAt() == null && $post->getisPublished()){
                $post->setPublishedAt($fecha_actual);
            }
            // handle the entities
            $entityManager=$this->getDoctrine()->getManager();
            //$entityManager->persist($post);
            $entityManager->flush();
            $this->addFlash('success', 'Post modificado correctamente');
            // Pruebas para volver atrás
            //return $this->redirect($request->headers->get('referer'));
            //return $this->redirect($this->getRequest()->headers->get('referer'));
            return $this->redirectToRoute('app_posts');
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
        $post=$this->getDoctrine()->getRepository(Post::class)->find($id);
        $entityManager=$this->getDoctrine()->getManager();
        $entityManager->remove($post);
        $entityManager->flush();
        $this->addFlash('success', 'Post eliminado correctamente');
        return $this->redirectToRoute('app_posts');
    }


    /**
     * @Route("/tag/form", name="app_tag_form")
     */
    public function tagForm(Request $request)
    {
        $form=$this->createFormBuilder(null)
            ->add('query',TextType::class)
            ->add('search',SubmitType::class)
            ->getForm();

        $form->handleRequest($request);
        //$error=$form->getErrors();

        if($form->isSubmitted() && $form->isValid()){
            // Capturo etiqueta ingresada
            $tag_input=$form->getData();
            $query=$tag_input['query'];

            return $this->redirectToRoute('app_tag_search',['etiqueta' => $query]);
        }

        return $this->render('post/searchTag.html.twig',[
        'form'=>$form->createView()
        ]);
    }

    /**
     * @Route("/tag/{etiqueta}/search", name="app_tag_search")
     */
    public function searchTag(Request $request, PaginatorInterface $paginator, $etiqueta){
        // Busco en la tabla de etiquetas ese nombre de etiqueta
        $tag=$this->getDoctrine()->getRepository(Tag::class)->findOneByTag($etiqueta);
        if(is_null($tag)){
            $this->addFlash('warning', 'Etiqueta no encontrada');
            return $this->redirectToRoute('app_homepage');
        }
        // Obtengo los posts con esa etiqueta
        $post_tag=$tag->getPosts();
        // Me quedo con los posts publicados con esa etiqueta
        $posts=[];
        foreach ($post_tag as $post) {
            // Si el post tiene fecha de publiación, lo agrego a la salida
            if(!is_null($post->getpublishedAt())){
                array_push($posts,$post);
            }
        }
        if(empty($posts)){
            $this->addFlash('warning', 'No hay posts publicados con esa etiqueta');
            return $this->redirectToRoute('app_homepage');
        }

        $pagination = $paginator->paginate(
            $posts, // query no el resultado
            $request->query->getInt('page', 1), // número de página
            3 // límite por página
        );

        return $this->render('home/home.html.twig',['pagination'=>$pagination]);
    }
}
