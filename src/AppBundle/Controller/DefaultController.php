<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Comment;
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
    public function showAction(Post $post, Request $request) {

$form = null;
        //jeżeli użytkownik jest zalogowany
        if ($user = $this->getUser()) {


            $comment = new Comment();
            $comment->setPost($post);
            $comment->setUser($user);

            $form = $this->createForm(CommentType::class, $comment);
            $form->handleRequest($request);

            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $em->persist($comment);
                $em->flush();

                $this->addFlash('succes', 'Komentarz został dodany!');
                return $this->redirectToRoute('news_show', ['id' => $post->getId()]);
            }
        }


        return $this->render('default/news.html.twig', [
                    'post' => $post,
                    'form' => is_null($form) ? $form : $form->createView()
        ]);
    }

}
