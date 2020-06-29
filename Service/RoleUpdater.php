<?php

namespace Stewie\UserBundle\Service;

// use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
// use Symfony\Component\Filesystem\Filesystem;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\ORM\EntityManagerInterface;
// use Stewie\UserBundle\Entity\Role;
// use Stewie\UserBundle\Entity\User;

class RoleUpdater extends AbstractController
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        // parent::__construct();
        $this->em = $em;
    }

    public function assignAdmins(){

        $roleRepo = $this->em->getRepository('StewieUserBundle:Role');
        $groupRepo = $this->em->getRepository('StewieUserBundle:Group');

        $roles = $roleRepo->findAll();
        $group = $groupRepo->findOneByName('App Admin');

        // remove all Roles
        foreach ($group->getGroupRoles() as &$role){
            $group->removeGroupRole($role);
        }

        // add all roles
        foreach ($roles as &$role){
            $group->addGroupRole($role);
        }

        $this->em->persist($group);
        $this->em->flush();

        // update all user in group
        foreach ($group->getUsers() as &$user){
            $this->updateUser($user);
        }

        return 1;
    }

    public function updateUser($user){

        $roleList = array();

        // get roles assigned to user
        foreach ($user->getUserRoles() as &$role){

            array_push($roleList, $role->getName());
            }

        // get roles assigned to any group of this user
        foreach ($user->getGroups() as &$group){

            foreach ($group->getGroupRoles() as &$role){

                array_push($roleList, $role->getName());

            }

        }

        // set all unique roles
        $uniqueRoles = array();

        foreach (array_unique($roleList) as &$role){

            array_push($uniqueRoles, $role);

        }

        $user->setRoles($uniqueRoles);

        $this->em->persist($user);
        $this->em->flush();

        return true;
    }

    public function updateGroup($group){
        
        // update all user in group
        foreach ($group->getUsers() as &$user){
            $this->updateUser($user);
        }

        return 1;
    }
}
