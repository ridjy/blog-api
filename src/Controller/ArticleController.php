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
use FOS\RestBundle\Request\ParamFetcherInterface;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Controller\Annotations\RequestParam;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Nelmio\ApiDocBundle\Annotation as Doc;

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
     * @Rest\Get("/api/listarticles", name="app_article_list")
     * @QueryParam(
     *     name="order",
     *     requirements="asc|desc",
     *     default="asc",
     *     description="Sort order (asc or desc)"
     * )
     */
    public function listArticles(ParamFetcherInterface $paramFetcher)
    {
        //dump($paramFetcher->get('order'));
    }

    /**
     * @Get("/api/searcharticles", name="app_article_searc")
     * @RequestParam(
     *     name="search",
     *     requirements="[a-zA-Z0-9]",
     *     default=null,
     *     nullable=true,
     *     description="Search query to look for articles"
     * )
     */
    public function searchAction($search)
    {
       //si param_fetcher_listener: force dans config alors on peut injecter directement comme $saerch
    }


    /**
     * @Get(
     *     path = "api/articles/{id}",
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
    public function createActionRest(Article $article, ValidatorInterface $validator)
    {
        $errors = $validator->validate($article);

        if (count($errors)) {
            return new Response($errors,Response::HTTP_BAD_REQUEST);
        }
        $em = $this->getDoctrine()->getManager();
        $em->persist($article);
        $em->flush();
        return new Response('OK', Response::HTTP_CREATED);
    }

    /**
     * @Route("/api/article/liste", name="article_list", methods={"GET"})
     */
    public function listAction(SerializerInterface $serializer)
    {
        $articles = $this->getDoctrine()->getRepository(Article::class)->findAll();
        $data = $serializer->serialize($articles, 'json');

        $response = new Response($data);
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    /**
     * @Rest\View(StatusCode = 200)
     * @Rest\Put(
     *     path = "api/articles/update/{id}",
     *     name = "app_article_update",
     *     requirements = {"id"="\d+"}
     * )
     */
    public function updateAction(Article $article, ValidatorInterface $validator,Request $request, SerializerInterface $serializer)
    {
        $data = $request->getContent();
        $newArticle = $serializer->deserialize($data, 'App\Entity\Article', 'json');

        /*$violations = $validator->validate($article);
        if (count($violations)) {
            $message = 'The JSON sent contains invalid data. Here are the errors you need to correct: ';
            foreach ($violations as $violation) {
                $message .= sprintf("Field %s: %s ", $violation->getPropertyPath(), $violation->getMessage());
            }

            throw new ResourceValidationException($message);
        }*/
        $article->setTitle($newArticle->getTitle());
        $article->setContent($newArticle->getContent());

        $this->getDoctrine()->getManager()->flush();

        return new Response('OK', Response::HTTP_CREATED);
    }

    /**
     * @Rest\View(StatusCode = 204)
     * @Rest\Delete(
     *     path = "api/article/delete/{id}",
     *     name = "app_article_delete",
     *     requirements = {"id"="\d+"}
     * )
     */
    public function deleteAction(Article $article)
    {
        $this->getDoctrine()->getManager()->remove($article);
        $this->getDoctrine()->getManager()->flush();
        return new Response('DELETED', Response::HTTP_NO_CONTENT);
    }

}//end controller
