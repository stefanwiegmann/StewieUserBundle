<?php

namespace App\Stefanwiegmann\UserBundle\Repository;

use App\Stefanwiegmann\UserBundle\Entity\User;
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
      foreach ($user->getUserRoles() as &$role){

        array_push($roles, $role->getName());
        }

      // get roles assigned to any group of this user
      foreach ($user->getGroups() as &$group){

        foreach ($group->getGroupRoles() as &$role){

          array_push($roles, $role->getName());

          }

        }

      // set all unique roles
      $uniqueRoles = array();
      foreach (array_unique($roles) as &$role){

        array_push($uniqueRoles, $role);

        }

      $user->setRoles($uniqueRoles);

      $em->persist($user);
      $em->flush();

      return true;

    }
}
