<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Post;
use AppBundle\Form\CommentType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller {

    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request) {
        
        

        $qb = $this->getDoctrine()
                ->getManager()
                ->createQueryBuilder()
                ->from('AppBundle:Post', 'p')
                ->select('p');

        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
                $qb, $request->query->get('page', 1), 2
        );

        return $this->render('default/index.html.twig', ['posts' => $pagination]);
    }

    /**
     * @Route("/news/{id}", name="news_show")
     */
    public function showAction(Post $post) {
        

        $form = $this->createForm(CommentType::class);

        return $this->render('default/news.html.twig', [
                    'post' => $post,
                    'form' => $form->createView()
        ]);
    }

}
