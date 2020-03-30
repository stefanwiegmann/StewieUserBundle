<?php

namespace App\Stefanwiegmann\UserBundle\Controller\Role;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
// use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
// use Symfony\Component\HttpFoundation\JsonResponse;

/**
  * @IsGranted("ROLE_USER_ADMIN")
  */

class RemoveController extends AbstractController
{
  /**
  * @Route("/user/role/group/remove", name="sw_user_role_group_remove")
  */
  public function groupAction(Request $request)
  {

    if($request->isXmlHttpRequest()) {

      $groupId   = $request->request->get('groupId');
      $roleId   = $request->request->get('roleId');

      $groupRepo = $this->getDoctrine()
        ->getRepository('StefanwiegmannUserBundle:Group');
      $group = $groupRepo->findOneById($groupId);

      $roleRepo = $this->getDoctrine()
        ->getRepository('StefanwiegmannUserBundle:Role');
      $role = $roleRepo->findOneById($roleId);

      $em = $this->getDoctrine()->getManager();
      $role->removeGroup($group);
      $em->persist($role);
      $em->flush();

      // refresh roles for that user
      $groupRepo->refreshRoles($group);

      $this->addFlash(
        'success',
        $group->getName().' was removed from Role '.$role->getName().'!'
        );

      $response = array("code" => 100, "success" => true, "groupId" => $groupId, "roleId" => $roleId);
      return new Response(json_encode($response));

    } else {
      return $this->redirectToRoute('sw_user_role_list');
    }
  }

  /**
  * @Route("/user/role/user/remove", name="sw_user_role_user_remove")
  */
  public function userAction(Request $request)
  {

    if($request->isXmlHttpRequest()) {

      $userId   = $request->request->get('userId');
      $roleId   = $request->request->get('roleId');

      $userRepo = $this->getDoctrine()
        ->getRepository('StefanwiegmannUserBundle:User');
      $user = $userRepo->findOneById($userId);

      $roleRepo = $this->getDoctrine()
        ->getRepository('StefanwiegmannUserBundle:Role');
      $role = $roleRepo->findOneById($roleId);

      $em = $this->getDoctrine()->getManager();
      $role->removeUser($user);
      $em->persist($role);
      $em->flush();

      // refresh roles for that user
      $userRepo->refreshRoles($user);

      $this->addFlash(
        'success',
        $user->getFirstName().' '.$user->getLastName().' ('.$user->getUserName().') was removed from Role '.$role->getName().'!'
        );

      $response = array("code" => 100, "success" => true, "userId" => $userId, "roleId" => $roleId);
      return new Response(json_encode($response));

    } else {
      return $this->redirectToRoute('sw_user_role_list');
    }
  }
}
