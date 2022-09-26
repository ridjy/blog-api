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

}