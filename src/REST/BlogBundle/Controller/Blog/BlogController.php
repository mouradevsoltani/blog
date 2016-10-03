<?php

namespace REST\BlogBundle\Controller\Blog;

use FOS\RestBundle\Controller\Annotations\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;


/**
 * Class RestBlogController
 */
class BlogController extends Controller
{
    /**
     * Blog index
     *
     * @Route("/", name="blog_index")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $articles = $em->getRepository('RESTBlogBundle:Article')->findBy(
            array(),
            array('dateCreation' => 'DESC')
        );
        return $this->render('@RESTBlog/blog/index.html.twig', array(
            'articles' => $articles
        ));
    }


    /**
     * Get all Articles
     *
     * @Route("/api/v1/articles", name="api_blog_articles", options={"expose"=true})
     * @Method("GET")
     * @return array
     */
    public function getArticlesAction()
    {
        $em = $this->getDoctrine()->getManager();
        $articles = $em->getRepository('RESTBlogBundle:Article')->findBy(
            array(),
            array('dateCreation' => 'DESC')
        );

        $data = array();
        $i = 0;
        foreach ($articles as $article){
            $object = array(
                'id' => $article->getId(),
                'titre' => $article->getTitre(),
                'contenu' => $article->getContenu(),
                'dateCreation' => $article->getDateCreation()->format("d/m/Y"),
                'dateModification' => $article->getDateModification()->format("d/m/Y"),
                'auteur' => $article->getAuteur()->getUsername()
            );
            $data[$i] =$object;
            $i++;
        }

        return new JsonResponse($data);
    }
}
