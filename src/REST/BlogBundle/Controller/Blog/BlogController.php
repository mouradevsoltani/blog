<?php

namespace REST\BlogBundle\Controller\Blog;

use FOS\RestBundle\Controller\Annotations\Route;
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
        $articles = $this->getArticles();
        // serialization des articles
        $serializer = $this->get('jms_serializer');
        $serializer->serialize($articles, 'json');

        return $this->render('@RESTBlog/blog/index.html.twig', array('articles'=>$articles));
    }

    /**
     * Get all Articles
     *
     * @return array
     */
    public function getArticles()
    {
        $em = $this->getDoctrine()->getManager();
        $articles = $em->getRepository('RESTBlogBundle:Article')->findBy(
            array(),
            array('dateCreation' => 'DESC')
        );

        $data = array();

        foreach ($articles as $article){
            $object = array(
                'id' => $article->getId(),
                'titre' => $article->getTitre(),
                'contenu' => $article->getContenu(),
                'dateCreation' => $article->getDateCreation(),
                'dateModification' => $article->getDateModification(),
                'auteur' => $article->getAuteur()->getUsername()
            );
            $data[] =$object;
        }
        return array('data' => $data);
    }
}
