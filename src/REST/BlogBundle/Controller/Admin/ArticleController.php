<?php

namespace REST\BlogBundle\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Finder\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use REST\BlogBundle\Entity\Article;
use REST\BlogBundle\Form\ArticleType;

/**
 * Article controller.
 *
 * @Route("/article")
 */
class ArticleController extends Controller
{
    /**
     * Lists all Article entities.
     *
     * @Route("/", name="article_index")
     * @Method("GET")
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser(); // utilisateur courant

        // Récupération de tous les articles pour l'utilsateur courant (super admin)
        if ($user->hasRole("ROLE_SUPER_ADMIN")) {
            $articles = $em->getRepository('RESTBlogBundle:Article')->findBy(
                array(),
                array('dateCreation' => 'DESC')
            );
        } else {
            // Récupération d'articles de l'utilsateur courant (Membre)
            $articles = $em->getRepository('RESTBlogBundle:Article')->findBy(
                array('auteur' => $user),
                array('dateCreation' => 'DESC')
            );
        }

        return $this->render('@RESTBlog/article/index.html.twig', array(
            'articles' => $articles,
        ));
    }

    /**
     * Creates a new Article entity.
     *
     * @Route("/new", name="article_new")
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function newAction(Request $request)
    {
        $article = new Article();
        $article->setAuteur($this->getUser()); // initialisation de l'auteur (utilisateur courant)
        $form = $this->createForm('REST\BlogBundle\Form\ArticleType', $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($article);
            $em->flush();
            //Notification
            $this->addFlash('success', 'L\'enregistrement a été effectué avec succès');

            return $this->redirectToRoute('article_show', array('id' => $article->getId()));
        }

        return $this->render('@RESTBlog/article/new.html.twig', array(
            'article' => $article,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Article entity.
     *
     * @Route("/{id}", name="article_show")
     * @Method("GET")
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function showAction(Article $article)
    {
        // Vérifier si l'utilisateur est celui l'auteur ou le super admin pour accéder à l'article
        if ($this->getUser()->hasRole("ROLE_SUPER_ADMIN") == false && $this->getUser() != $article->getAuteur()) {
            throw new AccessDeniedException("Vous n'avez pas les droits suffisants pour accéder à cette page");
        }
        $deleteForm = $this->createDeleteForm($article);

        return $this->render('@RESTBlog/article/show.html.twig', array(
            'article' => $article,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Article entity.
     *
     * @Route("/{id}/edit", name="article_edit")
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function editAction(Request $request, Article $article)
    {
        // Vérifier si l'utilisateur est celui l'auteur ou le super admin pour modifier l'article
        if ($this->getUser()->hasRole("ROLE_SUPER_ADMIN") == false && $this->getUser() != $article->getAuteur()) {
            throw new AccessDeniedException("Vous n'avez pas les droits suffisants pour accéder à cette page");
        }

        $deleteForm = $this->createDeleteForm($article);
        $editForm = $this->createForm('REST\BlogBundle\Form\ArticleType', $article);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($article);
            $em->flush();
            //Notification
            $this->addFlash('success', 'La mise à jour a été effectuée avec succès');

            return $this->redirectToRoute('article_edit', array('id' => $article->getId()));
        }

        return $this->render('@RESTBlog/article/edit.html.twig', array(
            'article' => $article,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a Article entity.
     *
     * @Route("/{id}", name="article_delete")
     * @Method("DELETE")
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function deleteAction(Request $request, Article $article)
    {
        // Vérifier si l'utilisateur est celui l'auteur ou le super admin pour supprimer l'article
        if ($this->getUser()->hasRole("ROLE_SUPER_ADMIN") == false && $this->getUser() != $article->getAuteur()) {
            throw new AccessDeniedException("Vous n'avez pas les droits suffisants pour accéder à cette page");
        }

        $form = $this->createDeleteForm($article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($article);
            $em->flush();
            //Notification
            $this->addFlash('success', 'La suppression a été effectuée avec succès');
        }

        return $this->redirectToRoute('article_index');
    }

    /**
     * Creates a form to delete a Article entity.
     *
     * @param Article $article The Article entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Article $article)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('article_delete', array('id' => $article->getId())))
            ->setMethod('DELETE')
            ->getForm();
    }
}
