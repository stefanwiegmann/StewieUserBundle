<?php

namespace App\Stefanwiegmann\UserBundle\Repository;

use App\Stefanwiegmann\UserBundle\Entity\User;
// use App\Stefanwiegmann\UserBundle\Entity\Role;
// use App\Stefanwiegmann\UserBundle\Entity\Group;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

class UserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function refreshRoles($user){
      $em = $this->getEntityManager();

      $roles = array();

      // get roles assigned to user
      foreach ($user->getUserRole() as &$role){

        array_push($roles, $role->getName());
        }

      // get roles assigned to any group of this user
      foreach ($user->getGroups() as &$group){

        foreach ($group->getGroupRole() as &$role){

          array_push($roles, $role->getName());

          }

        }

      // set all roles
      $user->setRoles(array_unique($roles));

      $em->persist($user);
      $em->flush();

      return true;

    }
}
