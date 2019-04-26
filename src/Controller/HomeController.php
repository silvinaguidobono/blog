<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Post;
use App\Entity\User;
use Knp\Component\Pager\PaginatorInterface;
//use Knp\Bundle\PaginatorBundle\Pagination;

class HomeController extends AbstractController {
    /**
     * 
     * @Route("/",name="app_homepage")
     */
    public function homepage(Request $request, PaginatorInterface $paginator){
        // mostrar solo los posts publicados
        //$posts=$this->getDoctrine()->getRepository(Post::class)->findPublishedPosts();
        //return $this->render('home/home.html.twig',['posts'=>$posts]);

        // Incorporo paginación de los post publicados
        // Agrego $request y $paginator como parámetros
        $entityManager = $this->getDoctrine()->getManager();
        $dql = "SELECT p FROM App\Entity\Post p WHERE p.publishedAt IS NOT NULL";
        $query = $entityManager->createQuery($dql);

        //$paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $query, // query no el resultado
            $request->query->getInt('page', 1), // número de página
            3 // límite por página
        );
        return $this->render('home/home.html.twig',['pagination'=>$pagination]);

    }
}
