<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Author;
use JMS\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Article;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations as Rest;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use FOS\RestBundle\Request\ParamFetcherInterface;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Controller\Annotations\RequestParam;

class AuthorController extends AbstractController
{
    /**
     * @Route("/authors/{id}", name="author_show")
     */
    public function showAction(Author $author,SerializerInterface $serializer)
    {
        //$article = $this->getDoctrine()->getRepository(Article::Class)->findOneById($id);

        /*$author = new Author();
        $author->setFullname('Sarah Khalil');
        $author->setBiography('Ma super biographie.');
        $author->getArticles()->add($article);*/

        $data =  $serializer->serialize($author, 'json');

        $response = new Response($data);
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    /**
     * @Route("/authors", name="author_create", methods={"POST"})
     */
    public function createAction(Request $request,SerializerInterface $serializer)
    {
        $data = $request->getContent();
        $author = $serializer->deserialize($data, 'App\Entity\Author', 'json');

        $em = $this->getDoctrine()->getManager();
        $em->persist($author);
        $em->flush();

        return new Response('', Response::HTTP_CREATED);
    }

    /**
     * @Rest\Get("/api/author/list", name="app_author_list")
     */
    public function listAuthors(SerializerInterface $serializer)
    {
        $authors = $this->getDoctrine()->getRepository(Author::Class)->findAll();
        
        $data =  $serializer->serialize($authors, 'json');

        $response = new Response($data);
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    /**
     * @Rest\Get("/api/author/search", name="app_author_search")
     * @Rest\QueryParam(
     *     name="keyword",
     *     requirements="[a-zA-Z0-9]",
     *     nullable=true,
     *     description="The keyword to search for."
     * )
     * @Rest\QueryParam(
     *     name="order",
     *     requirements="asc|desc",
     *     default="asc",
     *     description="Sort order (asc or desc)"
     * )
     * @Rest\QueryParam(
     *     name="limit",
     *     requirements="\d+",
     *     default="20",
     *     description="Max number of movies per page."
     * )
     * @Rest\QueryParam(
     *     name="offset",
     *     requirements="\d+",
     *     default="1",
     *     description="The pagination offset"
     * )
     * @Rest\View()
     */
    public function listAction(ParamFetcherInterface $paramFetcher)
    {
        $pager = $this->getDoctrine()->getRepository(Author::Class)->search(
            $paramFetcher->get('keyword'),
            $paramFetcher->get('order'),
            $paramFetcher->get('limit'),
            $paramFetcher->get('offset')
        );

        return $pager->getCurrentPageResults();
    }

}//fin controller
