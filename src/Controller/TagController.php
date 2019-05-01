<?php

namespace App\Controller;

use App\Entity\Tag;
use App\Form\TagType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Repository\TagRepository;

class TagController extends AbstractController
{
    /**
     * @Route("/tag", name="tag")
     */
    public function index()
    {
        return $this->render('tag/index.html.twig', [
            'controller_name' => 'TagController',
        ]);
    }

    /**
     * @Route("/tag/{id}/view", name="app_tag_view")
     */
    public function viewTag($id){
        $tag=$this->getDoctrine()->getRepository(Tag::class)->find($id);
        return $this->render('tag/viewTag.html.twig',['tag'=>$tag]);
    }

    /**
     * @Route("/tag/new", name="app_tag_new")
     */
    public function newTag(Request $request){
        // crea nuevo objeto Tag
        $tag=new Tag();

        // crear formulario
        $form=$this->createForm(TagType::class,$tag);

        // handle the request
        $form->handleRequest($request);
        $error=$form->getErrors();

        if($form->isSubmitted() && $form->isValid()){
            // data capture
            $tag=$form->getData();
            // handle the entities
            $entityManager=$this->getDoctrine()->getManager();
            $entityManager->persist($tag);
            $entityManager->flush();
            $this->addFlash('success', 'Etiqueta insertada correctamente');
            return $this->redirectToRoute('app_admin_tags');
        }
        // render the form
        return $this->render('tag/addTag.html.twig',[
            'error'=>$error,
            'form'=>$form->createView()
        ]);
    }

    /**
     * @Route("/tag/{id}/edit", name="app_tag_edit")
     */
    public function editTag($id,Request $request){
        $tag=$this->getDoctrine()->getRepository(Tag::class)->find($id);

        $form=$this->createForm(TagType::class,$tag);
        $form->handleRequest($request);
        $error=$form->getErrors();

        if($form->isSubmitted() && $form->isValid()){
            // handle the entities
            $entityManager=$this->getDoctrine()->getManager();
            $entityManager->flush();
            $this->addFlash('success', 'Etiqueta modificada correctamente');
            return $this->redirectToRoute('app_admin_tags');
        }

        // render the form
        return $this->render('tag/editTag.html.twig',[
            'error'=>$error,
            'form'=>$form->createView()
        ]);

    }

    /**
     * @Route("/tag/{id}/delete", name="app_tag_delete")
     */
    public function deleteTag($id, Request $request){
        $tag=$this->getDoctrine()->getRepository(Tag::class)->find($id);
        $entityManager=$this->getDoctrine()->getManager();
        $entityManager->remove($tag);
        $entityManager->flush();
        $this->addFlash('success', 'Etiqueta eliminada correctamente');
        return $this->redirectToRoute('app_admin_tags');
    }

    /**
     * @Route("/tag-test", name="app_tag_test", methods={"POST"})
     */
    public function tagTest(Request $request, TagRepository $tag_repos){
        //takes tag from form
        $tag= $request->get("tag");
        // testing if exists in database
        $em=$this->getDoctrine()->getManager();
        $tag_isset=$tag_repos->findOneBy(['tag'=>$tag]);
        $res="used";
        if(is_null($tag_isset)){
            $res="unused";
        }else{
            $res="used";
        }
        //returns an AJAX Response
        return new Response($res);
    }


}
