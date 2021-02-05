<?php

namespace Stewie\UserBundle\Repository;

use Stewie\UserBundle\Entity\Group;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
// use Symfony\Component\DependencyInjection\ContainerInterface;
use Doctrine\ORM\EntityManagerInterface;

class GroupRepository extends ServiceEntityRepository
{
    private $em;

    public function __construct(EntityManagerInterface $em, ManagerRegistry $registry)
    {
        parent::__construct($registry, Group::class);
        $this->em = $em;
    }

    // public function refreshRoles($group){
    //
    //   // $em = $this->container->get('doctrine')->getManager();
    //   $userRepo = $this->em->getRepository('StewieUserBundle:User');
    //
    //   // get users assigned to group and refresh
    //   foreach ($group->getUsers() as &$user){
    //
    //     // set all roles
    //     $userRepo->refreshRoles($user);
    //
    //     }
    //
    //   return true;
    //
    // }

    public function updateUser($user){

      // $em = $this->container->get('doctrine')->getManager();
      $groupRepo = $this->em->getRepository('StewieUserBundle:Group');

      $groups = $groupRepo->findAll();

      // remove users from these groups
      foreach ($groups as &$group){

        // remove user from old groups
        if ($group->getUsers()->contains($user) && !$user->getGroups()->contains($group)) {
            $group->removeUser($user);
            $this->em->persist($group);
          }

        // add user to new groups
        elseif (!$group->getUsers()->contains($user) && $user->getGroups()->contains($group)) {
            $group->addUser($user);
            $this->em->persist($group);
          }

        }

      $this->em->flush();

      return true;

    }
}
