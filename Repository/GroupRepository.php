<?php

namespace App\Stefanwiegmann\UserBundle\Repository;

use App\Stefanwiegmann\UserBundle\Entity\Group;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Symfony\Component\DependencyInjection\ContainerInterface;

class GroupRepository extends ServiceEntityRepository
{
    private $container;

    public function __construct(ContainerInterface $container, ManagerRegistry $registry)
    {
        parent::__construct($registry, Group::class);
        $this->container = $container;
    }

    public function refreshRoles($group){

      $em = $this->container->get('doctrine')->getManager();
      $userRepo = $em->getRepository('StefanwiegmannUserBundle:User');

      // get users assigned to group and refresh
      foreach ($group->getUser() as &$user){

        // set all roles
        $userRepo->refreshRoles($user);

        }

      return true;

    }

    public function updateUser($user){

      $em = $this->container->get('doctrine')->getManager();
      $groupRepo = $em->getRepository('StefanwiegmannUserBundle:Group');

      $groups = $groupRepo->findAll();

      // remove users from these groups
      foreach ($groups as &$group){

        // remove user from old groups
        if ($group->getUser()->contains($user) && !$user->getGroups()->contains($group)) {
            $group->removeUser($user);
            $em->persist($group);
          }

        // add user to new groups
        if (!$group->getUser()->contains($user) && $user->getGroups()->contains($group)) {
            $group->addUser($user);
            $em->persist($group);
          }

        }

      $em->flush();

      return true;

    }
}
