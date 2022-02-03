<?php

namespace Stewie\UserBundle\Repository;

use Stewie\UserBundle\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

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

      // keep only unique roles in new array
      $uniqueRoles = array();
      foreach (array_unique($roles) as &$role){

        array_push($uniqueRoles, $role);

        }
    
      $user->setRoles($uniqueRoles);

      $em->persist($user);
      $em->flush();

      return true;

    }

    public function queryByAnyName($name, $user)
    {

        $query = $this->createQueryBuilder("u")
            ->select('u.id AS value, u.fullNameUsername AS text')
            // ->leftjoin('a.useragency', 'c')
            // ->groupBy('a.id')
            // ->distinct(true)
            // ->where('a.agencyName = :name')
            ->where('LOCATE(:name, u.firstName) > 0')
            ->setParameter('name', $name);

        $query->orWhere('LOCATE(:name, u.lastName) > 0')
        ->setParameter('name', $name);

        $query->orWhere('LOCATE(:name, u.username) > 0')
        ->setParameter('name', $name);

        return $query;
    }

    public function findByAnyName($name, $user)
    {
        return $this->queryByAnyName($name, $user)
            ->getQuery()
            ->getResult();
    }
}
