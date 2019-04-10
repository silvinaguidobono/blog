<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Post;
use App\Entity\User;

class HomeController extends AbstractController {
    /**
     * 
     * @Route("/",name="app_homepage")
     */
    public function homepage(){
        if(!is_null($this->getUser())){
            $user=$this->getUser();
            $user_id=$user->getId();
        }else{
            $user_id=0;
        }
        // mostrar solo los posts publicados
        $posts=$this->getDoctrine()->getRepository(Post::class)->findPublishedPosts();
        //$posts=$this->getDoctrine()->getRepository(Post::class)->findAll();
        return $this->render('home/home.html.twig',['posts'=>$posts,'userId'=>$user_id]);
        //return $this->render('home/home.html.twig');
    }
}
