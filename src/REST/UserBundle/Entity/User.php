<?php

namespace REST\UserBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="utilisateurs")
 */
class User extends BaseUser
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\OneToMany(targetEntity="REST\BlogBundle\Entity\Article", mappedBy="auteur", cascade={"remove"})
     */
    private $articles;

    public function __construct()
    {
        parent::__construct();
        $this->roles = array('ROLE_ADMIN'); // Assignation de ROLE_ADMIN par défaut
        $this->articles = new ArrayCollection();
    }

    /**
     * Add article
     *
     * @param \REST\BlogBundle\Entity\Article $article
     *
     * @return User
     */
    public function addArticle(\REST\BlogBundle\Entity\Article $article)
    {
        $this->articles[] = $article;

        return $this;
    }

    /**
     * Remove article
     *
     * @param \REST\BlogBundle\Entity\Article $article
     */
    public function removeArticle(\REST\BlogBundle\Entity\Article $article)
    {
        $this->articles->removeElement($article);
    }

    /**
     * Get articles
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getArticles()
    {
        return $this->articles;
    }
}
