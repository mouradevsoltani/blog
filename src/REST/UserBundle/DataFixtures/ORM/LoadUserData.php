<?php

namespace REST\UserBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadUserData extends AbstractFixture implements ContainerAwareInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;
    /**
     * {@inheritDoc}
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {

        $factory = $this->container->get('security.encoder_factory');
        /** @var $manager \FOS\UserBundle\Doctrine\UserManager */
        $manager = $this->container->get('fos_user.user_manager');
        /** @var $user \REST\UserBundle\Entity\User */

        //Création d'un utilisateur Super Admin
        $user = $manager->createUser();
        $user->setUsername('admin');
        $user->setEmail('admin@blog.com');
        $user->setRoles(array('ROLE_SUPER_ADMIN'));
        $user->setEnabled(true);
        $encoder = $factory->getEncoder($user);
        $password = $encoder->encodePassword('admin', $user->getSalt());
        $user->setPassword($password);
        $manager->updateUser($user);

        unset($user);

        //Création d'un utilisateur Membre
        $user = $manager->createUser();
        $user->setUsername('mourad');
        $user->setEmail('mourad@dev.com');
        $user->setRoles(array('ROLE_ADMIN'));
        $user->setEnabled(true);
        $encoder = $factory->getEncoder($user);
        $password = $encoder->encodePassword('mourad', $user->getSalt());
        $user->setPassword($password);
        $manager->updateUser($user);

    }
}
