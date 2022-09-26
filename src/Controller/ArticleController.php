<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Article;
use JMS\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations as Rest;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

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
     * @Get(
     *     path = "/articles/{id}",
     *     name = "app_article_show",
     *     requirements = {"id"="\d+"}
     * )
     */
    public function showAction(Article $article,SerializerInterface $serializer)
    {
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
     * @Rest\Post("/api/articles_rest")
     * @Rest\View(StatusCode = 201)
     * @ParamConverter("article", converter="fos_rest.request_body")
     */
    public function createActionRest(Article $article)
    {
        $em = $this->getDoctrine()->getManager();
        $em->persist($article);
        $em->flush();
        return new Response('OK', Response::HTTP_CREATED);
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
