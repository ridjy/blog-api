<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Article;
use JMS\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\Request;

class ArticleController extends AbstractController
{
    /**
     * @Route("/article", name="app_article")
     */
    public function index(): Response
    {
        return $this->render('article/index.html.twig', [
            'controller_name' => 'ArticleController',
        ]);
    }

    /**
     * @Route("/articles/{id}", name="article_show")
     */
    public function showAction(Article $article,SerializerInterface $serializer)
    {
        /*$article = new Article();
        $article
            ->setTitle('Mon premier article')
            ->setContent('Le contenu de mon article.')
        ;*/
        $data = $serializer->serialize($article, 'json');

        $response = new Response($data);
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    /**
     * @Route("/articles", name="article_create", methods={"POST"})
     */
    public function createAction(Request $request, SerializerInterface $serializer)
    {
        //il faut que le json soit bien formé
        $data = $request->getContent();
        $article = $serializer->deserialize($data, 'App\Entity\Article', 'json');

        $em = $this->getDoctrine()->getManager();
        $em->persist($article);
        $em->flush();

        return new Response('', Response::HTTP_CREATED);
    }

    /**
     * @Route("/article/liste", name="article_list", methods={"GET"})
     */
    public function listAction(SerializerInterface $serializer)
    {
        $articles = $this->getDoctrine()->getRepository(Article::class)->findAll();
        $data = $serializer->serialize($articles, 'json');

        $response = new Response($data);
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

}//end controller
